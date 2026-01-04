<?php

declare(strict_types=1);

namespace App\Modules\Auth\Action;

use App\Application\Constants\HttpStatus;
use App\Application\Constants\Messages;
use App\Modules\User\Domain\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;

class LoginAction extends AuthAction
{
    protected function action(): Response
    {
        $data = $this->getFormData();

        // Simple validation
        if (empty($data['email']) || empty($data['password'])) {
            throw new HttpBadRequestException($this->request, Messages::FIELD_REQUIRED);
        }

        $email = strtolower(trim($data['email']));
        $password = $data['password'];

        try {
            $user = $this->userRepository->findByEmail($email);
            
            // Verify password
            if (!password_verify($password, $user->getPassword())) {
                throw new HttpUnauthorizedException($this->request, 'Credenciales inválidas');
            }

            // Generate Token
            $token = $this->authService->generateToken($user);

            $this->logger->info("User {$user->getId()} logged in.");

            return $this->respondWithData([
                'token' => $token,
                'user' => $user
            ]);

        } catch (UserNotFoundException $e) {
            // Security: Don't reveal user existence
            throw new HttpUnauthorizedException($this->request, 'Credenciales inválidas');
        }
    }
}
