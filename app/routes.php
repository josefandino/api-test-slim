<?php

declare(strict_types=1);

use App\Modules\User\Action\ListUsersAction;
use App\Modules\User\Action\ViewUserAction;
use App\Modules\User\Action\CreateUserAction;
use App\Modules\User\Action\UpdateUserAction;
use App\Modules\User\Action\DeleteUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
        $group->post('', CreateUserAction::class);
        $group->map(['PUT', 'PATCH'], '/{id}', UpdateUserAction::class);
        $group->delete('/{id}', DeleteUserAction::class);
    });
};
