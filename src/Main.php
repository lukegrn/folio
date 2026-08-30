<?php

declare(strict_types=1);

namespace App;

use App\Framework\Config\Config;
use App\Framework\Middleware\LogRequest;
use App\Framework\Router\Router;
use App\Handlers\HelloHandler;
use App\Handlers\HelloJsonHandler;

class Main
{
    public function __invoke(): void
    {
        Config::set(dirname(__DIR__) . "/conf.ini");

        $commonMiddleware = [
            LogRequest::class
        ];

        LogRequest::setOutput(Config::get()->logOutput);

        $router = new Router();

        $router->GET("/", HelloHandler::class, $commonMiddleware);
        $router->GET("/hello/{name}", HelloHandler::class, $commonMiddleware);
        $router->GET("/json", HelloJsonHandler::class, $commonMiddleware);

        $router->run();
    }
}
