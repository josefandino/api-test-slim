<?php

declare(strict_types=1);

namespace App\Modules\Auth\Middleware;

use App\Modules\Auth\Domain\AuthServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

class JwtMiddleware implements MiddlewareInterface
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new HttpUnauthorizedException($request, 'Token de autenticación requerido');
        }

        $token = $matches[1];

        try {
            $decoded = $this->authService->verifyToken($token);
            
            // Inyectamos el usuario decodificado en el request para que los controladores lo usen si quieren
            $request = $request->withAttribute('user', $decoded);
            
        } catch (\Throwable $e) {
            throw new HttpUnauthorizedException($request, 'Token inválido o expirado');
        }

        return $handler->handle($request);
    }
}
