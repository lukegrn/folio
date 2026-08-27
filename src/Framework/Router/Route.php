<?php

declare(strict_types=1);

namespace App\Framework\Router;

use App\Framework\Handler\Handler;
use App\Framework\Middleware\Middleware;
use App\Framework\Router\Exceptions\InvalidHandlerException;
use App\Framework\Router\Exceptions\InvalidMiddlewareException;

class Route
{
    private Middleware|Handler $chain;

    /**
     * @param class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    public function __construct(string $handler, array $middlewares = [])
    {
        // Validate classes
        if ($handler != Handler::class && !is_subclass_of($handler, Handler::class)) {
            throw new InvalidHandlerException("Class $handler is not " . Handler::class . " or a child of it");
        }

        $this->chain = new $handler();

        while (!empty($middlewares)) {
            $middleware = array_pop($middlewares);
            if ($middleware != Middleware::class && !is_subclass_of($middleware, Middleware::class)) {
                throw new InvalidMiddlewareException("Class $middleware is not " . Middleware::class . " or a child of it");
            }

            $this->chain = new $middleware($this->chain);
        }
    }

    /**
     * @param array<string, mixed> $args
     */
    public function __invoke(array $args): void
    {
        ($this->chain)($args);
    }
}
