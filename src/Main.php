<?php

declare(strict_types=1);

namespace App;

use App\Framework\Router\Router;
use App\Handlers\HelloHandler;
use App\Handlers\HelloJsonHandler;

class Main
{
    public function __invoke(): void
    {
        $router = new Router();

        $router->GET("/", new HelloHandler());
        $router->GET("/hello/{name}", new HelloHandler());
        $router->GET("/json", new HelloJsonHandler());

        $router->run();
    }
}
