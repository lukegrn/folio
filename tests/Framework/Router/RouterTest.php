<?php

declare(strict_types=1);

use App\Framework\Handler\Handler;
use App\Framework\Router\Exceptions\DuplicateWildcareException;
use App\Framework\Router\Exceptions\RouteAlreadyRegisteredException;
use App\Framework\Router\Router;
use App\Framework\Router\Exceptions\UnmatchedCurlyBraceException;
use PHPUnit\Framework\TestCase;

class HelloHandler extends Handler
{
    protected function handle(array $args): void
    {
        if (array_key_exists('name', $args)) {
            echo 'hello, ' . $args['name'];
        } else {
            echo 'hello';
        }
    }
}

final class RouterTest extends TestCase
{
    public function testCanRunRegisteredRoute(): void
    {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router->GET('/', HelloHandler::class);
        $this->expectOutputString('hello');
        $router->run();
    }

    public function testReregisteringRouteThrowsException(): void
    {
        $router = new Router();
        $router->GET('/', HelloHandler::class);
        $this->expectException(RouteAlreadyRegisteredException::class);
        $router->GET('/', HelloHandler::class);
    }

    public function testUsesDefaultHandlerWhenRouteNotRegistered(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();
        $this->expectOutputString('Not found');
        $router->run();
    }

    public function testCanRegisterOneRouteWithMultipleMethods(): void
    {
        $router = new Router();
        $router->GET('/', HelloHandler::class);
        $router->POST('/', HelloHandler::class);

        $this->expectNotToPerformAssertions();
    }

    public function testSupportsGet(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $router = new Router();
        $router->GET("/", HelloHandler::class);

        $this->expectOutputString('hello');
        $router->run();
    }

    public function testSupportsHead(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $_SERVER['REQUEST_URI'] = '/';
        $router = new Router();
        $router->HEAD("/", HelloHandler::class);

        $this->expectOutputString('hello');
        $router->run();
    }

    public function testSupportsPut(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_SERVER['REQUEST_URI'] = '/';
        $router = new Router();
        $router->PUT("/", HelloHandler::class);

        $this->expectOutputString('hello');
        $router->run();
    }

    public function testSupportsPost(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/';
        $router = new Router();
        $router->POST("/", HelloHandler::class);

        $this->expectOutputString('hello');
        $router->run();
    }


    public function testSupportsPatch(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $_SERVER['REQUEST_URI'] = '/';
        $router = new Router();
        $router->PATCH("/", HelloHandler::class);

        $this->expectOutputString('hello');
        $router->run();
    }

    public function testSupportsDelete(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['REQUEST_URI'] = '/';
        $router = new Router();
        $router->DELETE("/", HelloHandler::class);

        $this->expectOutputString('hello');
        $router->run();
    }

    public function testCanMatchWildcards(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/hello/test';

        $router = new Router();
        $router->GET('/hello/{name}', HelloHandler::class);

        $this->expectOutputString('hello, test');
        $router->run();
    }

    public function testMalformedWildcardThrowsExceptions(): void
    {
        $router = new Router();
        $this->expectException(UnmatchedCurlyBraceException::class);
        $router->GET('/options/{option', HelloHandler::class);

        $this->expectException(DuplicateWildcareException::class);
        $router->GET('/options/{option}/{option}', HelloHandler::class);
    }
}
