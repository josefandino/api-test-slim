<?php

declare(strict_types=1);

use App\Modules\User\Domain\UserRepository;
use App\Modules\User\Infrastructure\PDOUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(PDOUserRepository::class),
        \App\Modules\Auth\Domain\AuthServiceInterface::class => function () {
            return new \App\Modules\Auth\Infrastructure\JwtAuthService(
                $_SERVER['JWT_SECRET'] ?? $_ENV['JWT_SECRET'] ?? 'default_secret_ignored_if_env_works',
                $_SERVER['JWT_EXPIRES_IN'] ?? $_ENV['JWT_EXPIRES_IN'] ?? '1 hour'
            );
        },
    ]);
};
