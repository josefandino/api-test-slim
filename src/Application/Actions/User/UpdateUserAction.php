<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = $this->resolveArg('id');
        $data = $this->getFormData();

        // Check if user exists first
        $user = $this->userRepository->findUserOfId($userId);

        // Update fields if present (Partial update supported via PATCH logic, but this is a PUT/PATCH mix)
        // For simplicity, we require the fields or assume null means no change? 
        // Let's assume PUT: full replacement or PATCH: partial. Let's do partial checks.
        
        $firstName = $data['firstName'] ?? $user->getFirstName();
        $lastName = $data['lastName'] ?? $user->getLastName();
        $email = $data['email'] ?? $user->getEmail();
        $password = isset($data['password']) ? password_hash($data['password'], PASSWORD_ARGON2ID) : $user->getPassword();

        // Create a new instance with updated data (since User is immutable-ish but we are replacing it essentially)
        // Actually, we could add setters to User, or just create a new object.
        // Let's create a new object to act as the updated state.
        $updatedUser = new User(
            $userId,
            $firstName,
            $lastName,
            $email,
            $password
        );

        $this->userRepository->update($updatedUser);

        $this->logger->info("User of id `${userId}` was updated.");

        return $this->respondWithData($updatedUser);
    }
}
