<?php

declare(strict_types=1);

namespace App\Modules\User\Domain;

use JsonSerializable;

class User implements JsonSerializable
{
    private ?string $id;

    private ?string $firstName;

    private string $lastName;

    private string $email;
    
    private ?string $password;

    public function __construct(?string $id, ?string $firstName, string $lastName, string $email, ?string $password = null)
    {
        $this->id = $id;
        $this->firstName = $firstName ? ucfirst($firstName) : null;
        $this->lastName = ucfirst($lastName);
        $this->email = strtolower($email);
        $this->password = $password;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
