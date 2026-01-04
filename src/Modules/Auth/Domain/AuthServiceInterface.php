<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain;

use App\Modules\User\Domain\User;

interface AuthServiceInterface
{
    /**
     * Generate a JWT token for a given user.
     * 
     * @param User $user
     * @return string The encoded JWT token
     */
    public function generateToken(User $user): string;

    /**
     * Verify and decode a token.
     * 
     * @param string $token
     * @return object The decoded token payload
     * @throws \Exception If token is invalid or expired
     */
    public function verifyToken(string $token): object;
}
