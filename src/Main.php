<?php

declare(strict_types=1);

namespace App;

use App\Framework\Router\Router;

class Main
{
    public function __invoke(): void
    {
        $router = new Router();

        $router->GET("/", function () {
            echo 'Hello, homepage!';
        });

        $router->GET("/{name}", function ($args) {
            echo 'Hello, ' . $args['name'] . '!';
        });

        $router->run();
    }
}
