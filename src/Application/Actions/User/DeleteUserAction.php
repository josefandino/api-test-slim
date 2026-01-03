<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = $this->resolveArg('id');

        $this->userRepository->delete($userId);

        $this->logger->info("User of id `${userId}` was deleted.");

        return $this->respondWithData(null, 200, 'User deleted successfully.');
    }
}
