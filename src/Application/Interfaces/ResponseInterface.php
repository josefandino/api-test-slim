<?php

declare(strict_types=1);

namespace App\Application\Interfaces;

/**
 * Contract for standardized API responses.
 * Enforces strict typing similar to a TypeScript interface.
 */
interface ResponseInterface
{
    public function getTotal(): int;

    public function getMessage(): string;

    /**
     * Equivalent to 'resp': boolean
     */
    public function isResp(): bool;

    /**
     * Equivalent to 'error': string | null
     */
    public function getErrorCode(): ?string;

    public function getStatus(): int;

    /**
     * Equivalent to 'data': T
     * @return mixed
     */
    public function getData();
}
