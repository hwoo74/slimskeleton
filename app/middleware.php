<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\BeforeMiddleware;
use App\Application\Middleware\AfterMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    //$app->add(BeforeMiddleware::class);         // 다 실행됨 ..
    //$app->add(AfterMiddleware::class);          // 다 실행됨 .. 
};
