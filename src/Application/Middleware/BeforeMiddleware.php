<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class BeforeMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        echo 'Before Middleware ';

        $response = $handler->handle($request);
        //$existingContent = (string) $response->getBody();

        //$response = new Response();
        //$response->getBody()->write('BEFORE ' . $existingContent);

        return $response;
    }
}
