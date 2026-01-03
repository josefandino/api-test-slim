<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Constants\ErrorCodes;
use JsonSerializable;

class ActionError implements JsonSerializable
{
    public const BAD_REQUEST = ErrorCodes::BAD_REQUEST;
    public const INSUFFICIENT_PRIVILEGES = ErrorCodes::INSUFFICIENT_PRIVILEGES;
    public const NOT_ALLOWED = ErrorCodes::NOT_ALLOWED;
    public const NOT_IMPLEMENTED = ErrorCodes::NOT_IMPLEMENTED;
    public const RESOURCE_NOT_FOUND = ErrorCodes::RESOURCE_NOT_FOUND;
    public const SERVER_ERROR = ErrorCodes::SERVER_ERROR;
    public const UNAUTHENTICATED = ErrorCodes::UNAUTHENTICATED;
    public const VALIDATION_ERROR = ErrorCodes::VALIDATION_ERROR;
    public const VERIFICATION_ERROR = ErrorCodes::VERIFICATION_ERROR;

    private string $type;

    private ?string $description;

    public function __construct(string $type, ?string $description = null)
    {
        $this->type = $type;
        $this->description = $description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description = null): self
    {
        $this->description = $description;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
