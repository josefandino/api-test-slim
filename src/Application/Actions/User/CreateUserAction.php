<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Constants\HttpStatus;
use App\Application\Constants\Messages;
use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class CreateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();

        // ... validation block ... (skipping unchanged code if possible? No, need to inject logic after validation)
        
        // Define validation rules - Clean and declarative like a DTO
        $userValidator = v::key('firstName', v::stringType()
                ->notEmpty()->setTemplate(Messages::NAME_REQUIRED)
                ->length(2, null)->setTemplate(Messages::NAME_MIN_LENGTH)
                ->length(null, 50)->setTemplate(Messages::NAME_MAX_LENGTH))
            ->key('lastName', v::stringType()
                ->notEmpty()->setTemplate(Messages::LASTNAME_REQUIRED)
                // Re-ordered and explicit
                ->length(null, 50)->setTemplate(Messages::LASTNAME_MAX_LENGTH)
                ->length(2, null)->setTemplate(Messages::LASTNAME_MIN_LENGTH))
            ->key('email', v::stringType()->notEmpty()->setTemplate(Messages::EMAIL_REQUIRED)
                ->callback(function ($input) {
                    $validator = new \Egulias\EmailValidator\EmailValidator();
                    // 1. Check strict RFC format
                    // 2. Check DNS records (MX) to ensure domain actually exists and accepts emails
                    return $validator->isValid($input, new \Egulias\EmailValidator\Validation\MultipleValidationWithAnd([
                        new \Egulias\EmailValidator\Validation\RFCValidation(),
                        new \Egulias\EmailValidator\Validation\DNSCheckValidation()
                    ]));
                })->setTemplate(Messages::EMAIL_INVALID))
            ->key('password', v::stringType()
                ->notEmpty()->setTemplate(Messages::PASSWORD_REQUIRED)
                ->length(6, null)->setTemplate(Messages::PASSWORD_MIN_LENGTH));

        try {
            $userValidator->assert($data);
        } catch (NestedValidationException $exception) {
            // Flatten errors to a single string or array
            $errors = implode(', ', $exception->getMessages());
            
            // Fallback for missing keys
            $errors = str_replace('must be present', Messages::FIELD_REQUIRED, $errors);
            
            throw new HttpBadRequestException($this->request, $errors);
        }

        $email = strtolower(trim($data['email']));

        // Check if email already exists
        try {
            $this->userRepository->findByEmail($email);
            // If checking successful, it means user exists
            // Security: Use generic message to prevent user enumeration
            throw new HttpBadRequestException($this->request, Messages::GENERIC_ERROR);
        } catch (UserNotFoundException $e) {
            // User not found, proceed safely
        }

        // Create User object (ID is null as it will be generated)
        $user = new User(
            null,
            trim($data['firstName']),
            trim($data['lastName']),
            $email,
            password_hash($data['password'], PASSWORD_ARGON2ID)
        );

        $createdUser = $this->userRepository->create($user);

        $this->logger->info("User created with id " . $createdUser->getId());

        return $this->respondWithData($createdUser, HttpStatus::CREATED);
    }
}
