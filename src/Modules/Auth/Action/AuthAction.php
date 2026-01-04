<?php

declare(strict_types=1);

namespace App\Modules\Auth\Action;

use App\Application\Actions\Action;
use App\Modules\Auth\Domain\AuthServiceInterface;
use App\Modules\User\Domain\UserRepository;
use Psr\Log\LoggerInterface;

abstract class AuthAction extends Action
{
    protected UserRepository $userRepository;
    protected AuthServiceInterface $authService;

    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        AuthServiceInterface $authService
    ) {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }
}
