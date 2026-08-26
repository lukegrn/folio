<?php

declare(strict_types=1);

use App\Framework\Router\Exceptions\DuplicateWildcareException;
use App\Framework\Router\Exceptions\RouteAlreadyRegisteredException;
use App\Framework\Router\Router;
use App\Framework\Router\Exceptions\UnmatchedCurlyBraceException;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testCanRunRegisteredRoute(): void
    {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $f = function () {
            echo 'ran';
        };

        $router->GET('/', $f);
        $this->expectOutputString('ran');
        $router->run();
    }

    public function testReregisteringRouteThrowsException(): void
    {
        $router = new Router();
        $router->GET('/', fn () => 'first');
        $this->expectException(RouteAlreadyRegisteredException::class);
        $router->GET('/', fn () => 'second');
    }

    public function testUsesDefaultHandlerWhenRouteNotRegistered(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();
        $this->expectOutputString('not found');
        $router->run();
    }

    public function testCanRegisterOneRouteWithMultipleMethods(): void
    {
        $router = new Router();
        $router->GET('/', fn () => 'first');
        $router->POST('/', fn () => 'second');

        $this->expectNotToPerformAssertions();
    }

    public function testSupportsAllVerbs(): void
    {
        $router = new Router();
        $called = "";
        foreach (['GET', 'POST', 'PATCH', 'HEAD', 'DELETE', 'PUT'] as $verb) {
            $router->$verb('/', function () use ($verb, &$called) {
                $called = $verb;
            });
        }


        foreach (['GET', 'POST', 'PATCH', 'HEAD', 'DELETE', 'PUT'] as $verb) {
            $_SERVER['REQUEST_METHOD'] = $verb;
            $_SERVER['REQUEST_URI'] = '/';

            $router->run();

            $this->assertEquals($called, $verb);
        }
    }

    public function testCanMatchWildcards(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/options/testoption';

        $router = new Router();
        $router->GET('/options/{option}', function () {
            echo 'called';
        });

        $this->expectOutputString('called');
        $router->run();
    }

    public function testHandlersAreCalledWithWildcards(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/options/testoption';

        $router = new Router();
        $router->GET('/options/{option}', function ($args) {
            echo $args['option'];
        });

        $this->expectOutputString('testoption');
        $router->run();
    }

    public function testMalformedWildcardThrowsExceptions(): void
    {
        $router = new Router();
        $this->expectException(UnmatchedCurlyBraceException::class);
        $router->GET('/options/{option', function ($args) {
            echo $args['option'];
        });

        $this->expectException(DuplicateWildcareException::class);
        $router->GET('/options/{option}/{option}', function ($args) {
            echo $args['option'];
        });
    }
}
