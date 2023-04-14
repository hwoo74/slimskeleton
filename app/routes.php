<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

//use App\Controller\TestController;
use App\Application\Middleware\FinalMiddleware;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    // leah test. 
    //$app->get('/test', 'TestController:test' );     //->add( FinalMiddleware::class );
    $app->get('/test', 'TestController:test' );     //->add( FinalMiddleware::class );                      // 작동안한다.... 
    //$app->get('/test', TestController::class . ':test' );     //->add( FinalMiddleware::class );          // 별거 없이 작동.
    //$app->get('/test', [ TestController::class, 'test' ] );     //->add( FinalMiddleware::class );        // 별거 없이 작동.

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
