<?php

declare(strict_types=1);

namespace App\Modules\User\Action;

use App\Application\Constants\HttpStatus;
use App\Application\Constants\Messages;
use App\Modules\User\Domain\User;
use App\Modules\User\Domain\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class UpdateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = $this->resolveArg('id');
        
        // Check if user exists first
        $user = $this->userRepository->findUserOfId($userId);
        
        $data = $this->getFormData();

        // Define validation rules - Keys are OPTIONAL (false 3rd arg) for partial updates
        $userValidator = v::key('firstName', v::stringType()
                ->notEmpty()->setTemplate(Messages::NAME_REQUIRED)
                ->length(2, null)->setTemplate(Messages::NAME_MIN_LENGTH)
                ->length(null, 50)->setTemplate(Messages::NAME_MAX_LENGTH), false)
            ->key('lastName', v::stringType()
                ->notEmpty()->setTemplate(Messages::LASTNAME_REQUIRED)
                ->length(null, 50)->setTemplate(Messages::LASTNAME_MAX_LENGTH)
                ->length(2, null)->setTemplate(Messages::LASTNAME_MIN_LENGTH), false)
            ->key('email', v::stringType()->notEmpty()->setTemplate(Messages::EMAIL_REQUIRED)
                ->callback(function ($input) {
                    $validator = new \Egulias\EmailValidator\EmailValidator();
                    return $validator->isValid($input, new \Egulias\EmailValidator\Validation\MultipleValidationWithAnd([
                        new \Egulias\EmailValidator\Validation\RFCValidation(),
                        new \Egulias\EmailValidator\Validation\DNSCheckValidation()
                    ]));
                })->setTemplate(Messages::EMAIL_INVALID), false)
            ->key('password', v::stringType()
                ->notEmpty()->setTemplate(Messages::PASSWORD_REQUIRED)
                ->length(6, null)->setTemplate(Messages::PASSWORD_MIN_LENGTH), false);

        try {
            $userValidator->assert($data);
        } catch (NestedValidationException $exception) {
            $errors = implode(', ', $exception->getMessages());
            $errors = str_replace('must be present', Messages::FIELD_REQUIRED, $errors);
            throw new HttpBadRequestException($this->request, $errors);
        }

        // Sanitization and Update Logic
        
        $firstName = isset($data['firstName']) ? trim($data['firstName']) : $user->getFirstName();
        $lastName = isset($data['lastName']) ? trim($data['lastName']) : $user->getLastName();
        
        $email = $user->getEmail();
        if (isset($data['email'])) {
            $newEmail = strtolower(trim($data['email']));
            // Compare lowercase to avoid false positives if DB is mixed case but logically same
            if ($newEmail !== strtolower($user->getEmail())) {
                // Check duplicate
                try {
                    $existingUser = $this->userRepository->findByEmail($newEmail);
                    // Compare IDs in lowercase to verify it's not the same user
                    if (strtolower($existingUser->getId()) !== strtolower($userId)) {
                        throw new HttpBadRequestException($this->request, Messages::GENERIC_ERROR);
                    }
                } catch (UserNotFoundException $e) {
                    // Safe to use
                }
                $email = $newEmail;
            }
        }

        $password = $user->getPassword();
        if (isset($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_ARGON2ID);
        }

        $updatedUser = new User(
            $userId,
            $firstName,
            $lastName,
            $email,
            $password
        );

        $this->userRepository->update($updatedUser);

        $this->logger->info("User of id {$userId} was updated.");

        return $this->respondWithData($updatedUser, HttpStatus::OK, Messages::USER_UPDATED);
    }
}
