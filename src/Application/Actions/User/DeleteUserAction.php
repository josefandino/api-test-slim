<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Constants\HttpStatus;
use App\Application\Constants\Messages;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = $this->resolveArg('id');

        try {
            $this->userRepository->delete($userId);
            $this->logger->info("User of id {$userId} was deleted.");
        } catch (DomainRecordNotFoundException $e) {
            // Idempotency: If user not found, treat as successful delete to prevent enumeration
            $this->logger->info("Delete request for non-existent user id {$userId} processed.");
        }

        return $this->respondWithData(null, HttpStatus::OK, Messages::USER_DELETED);
    }
}
