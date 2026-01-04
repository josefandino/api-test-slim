<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure;

use App\Modules\Auth\Domain\AuthServiceInterface;
use App\Modules\User\Domain\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeImmutable;

class JwtAuthService implements AuthServiceInterface
{
    private string $secret;
    private string $expiresIn;

    public function __construct(string $secret, string $expiresIn)
    {
        $this->secret = $secret;
        $this->expiresIn = $expiresIn;
    }

    public function generateToken(User $user): string
    {
        $issuedAt = new DateTimeImmutable();
        // Parse "2h" or simple seconds logic. 
        // For simplicity, let's assume strict format or just use consistent time.
        // If Env has "2h", we need to parse it or just use seconds in env.
        // Let's assume user might put "2 hours" or "7200". 
        // PHP's DateTime modify handles "2 hours".
        
        // Ensure we have a valid interval, default to 2 hours if generic
        $interval = $this->expiresIn;
        // Expand short syntax (2h -> 2 hours) for compatibility
        $interval = str_ireplace(['h', 'm'], [' hours', ' minutes'], $interval);
        
        if (!preg_match('/^[+\-]/', trim($interval))) {
            $interval = '+' . trim($interval);
        }
        
        $expire = $issuedAt->modify($interval);

        $payload = [
            'iat' => $issuedAt->getTimestamp(),
            'nbf' => $issuedAt->getTimestamp(),
            'exp' => $expire->getTimestamp(),
            'sub' => $user->getId(),
            'email' => $user->getEmail()
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verifyToken(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }
}
