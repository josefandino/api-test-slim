<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Interfaces\ResponseInterface;
use JsonSerializable;

class ActionPayload implements JsonSerializable, ResponseInterface
{
    private int $statusCode;

    /**
     * @var array|object|null
     */
    private $data;

    private ?ActionError $error;

    private ?string $message;
    
    private int $total;

    public function __construct(
        int $statusCode = 200,
        $data = null,
        ?ActionError $error = null,
        string $message = '',
        int $total = 0
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
        $this->message = $message;
        $this->total = $total;
    }

    public function getStatus(): int
    {
        return $this->statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array|null|object
     */
    public function getData()
    {
        return $this->data;
    }

    public function getError(): ?ActionError
    {
        return $this->error;
    }

    public function getErrorCode(): ?string
    {
        if ($this->error !== null) {
            return $this->error->getType();
        }
        
        if ($this->statusCode >= 400) {
            return 'API_ERROR';
        }

        return null;
    }

    public function getMessage(): string
    {
        // Fallback logic inside getter for consistency
         if (empty($this->message) && $this->error !== null) {
             return $this->error->getDescription() ?? 'An error occurred';
         }
         
        return $this->message ?? '';
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function isResp(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $payload = [
            'message' => $this->message,
            'resp' => $this->statusCode >= 200 && $this->statusCode < 300,
            'error' => null,
            'status' => $this->statusCode,
            'data' => $this->data ?? [],
            'total' => $this->total,
        ];

        // If error exists, populate error field and fallback message
        if ($this->error !== null) {
            $payload['error'] = $this->error->getType();
            
            // If message is empty, fallback to error description
             if (empty($payload['message'])) {
                 $payload['message'] = $this->error->getDescription() ?? 'An error occurred';
            }
        } elseif ($this->statusCode >= 400) {
            // General fallback for errors without ActionError
             $payload['error'] = 'API_ERROR';
        }

        return $payload;
    }
}
