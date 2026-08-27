<?php

declare(strict_types=1);

use App\Framework\Handler\Handler;
use App\Framework\Middleware\Middleware;
use App\Framework\Router\Exceptions\InvalidHandlerException;
use App\Framework\Router\Exceptions\InvalidMiddlewareException;
use App\Framework\Router\Route;
use PHPUnit\Framework\TestCase;

class RouteTestHandler extends Handler
{
    protected function handle(array $args): void
    {
        echo 'Hello, ' . $args['name'];
    }
}

class RouteTestOuterMiddleware extends Middleware
{
    protected function handle(array $args): void
    {
        echo "Outer middleware";
        ($this->next)($args);
    }
}

class RouteTestInnerMiddleware extends Middleware
{
    protected function handle(array $args): void
    {
        echo "Inner middleware";
        ($this->next)($args);
    }
}

class RouteTestBlockingMiddleware extends Middleware
{
    protected function handle(array $args): void
    {
        echo "Nothing after this";
    }
}

final class RouteTest extends TestCase
{
    public function testRouteCallsHandler(): void
    {
        $h = new Route(RouteTestHandler::class);

        $this->expectOutputString('Hello, test');
        $h(['name' => 'test']);
    }

    public function testRouteExecutesMiddlewareInOrder(): void
    {
        $h = new Route(RouteTestHandler::class, [
            RouteTestOuterMiddleware::class, RouteTestInnerMiddleware::class
        ]);

        $this->expectOutputString('Outer middlewareInner middlewareHello, test');
        $h(['name' => 'test']);
    }

    public function testRouteValidatesClassOfHandler(): void
    {
        $this->expectException(InvalidHandlerException::class);
        $h = new Route('string', [
            RouteTestOuterMiddleware::class, RouteTestInnerMiddleware::class
        ]);
    }

    public function testRouteValidatesClassOfMiddlewares(): void
    {
        $this->expectException(InvalidMiddlewareException::class);
        $h = new Route(RouteTestHandler::class, [
            RouteTestOuterMiddleware::class, 'string'
        ]);
    }

    public function testChainCancelsIfNextNotCalled(): void
    {
        $h = new Route(RouteTestHandler::class, [
            RouteTestOuterMiddleware::class,
            RouteTestBlockingMiddleware::class,
            RouteTestInnerMiddleware::class,
        ]);

        $this->expectOutputString('Outer middlewareNothing after this');
        $h(['name' => 'test']);
    }
}
