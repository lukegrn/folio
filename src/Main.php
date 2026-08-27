<?php

declare(strict_types=1);

namespace App;

use App\Framework\Middleware\LogRequest;
use App\Framework\Router\Router;
use App\Handlers\HelloHandler;
use App\Handlers\HelloJsonHandler;

class Main
{
    public function __invoke(): void
    {
        $commonMiddleware = [
            LogRequest::class
        ];

        $router = new Router();

        $router->GET("/", HelloHandler::class, $commonMiddleware);
        $router->GET("/hello/{name}", HelloHandler::class, $commonMiddleware);
        $router->GET("/json", HelloJsonHandler::class, $commonMiddleware);

        $router->run();
    }
}
