<?php

declare(strict_types=1);

namespace App\Framework\Router;

use App\Framework\Handler\Handler;
use App\Framework\Router\Builtins\BuiltinNotFoundHandler;
use App\Framework\Router\Exceptions\DuplicateWildcareException;
use App\Framework\Router\Exceptions\RouteAlreadyRegisteredException;
use App\Framework\Router\Exceptions\UnmatchedCurlyBraceException;

class Router
{
    private array $routes;
    private string $DEFAULT_HANDLER_KEY = "__default_handler";

    /**
     * @param ?class-string<Handler> $handler
     */
    public function __construct(?string $default_handler = null)
    {
        $this->routes = [
            'GET' => [],
            'HEAD' => [],
            'POST' => [],
            'PUT' => [],
            'DELETE' => [],
            'PATCH' => [],
            $this->DEFAULT_HANDLER_KEY => []
        ];

        if ($default_handler == null) {
            $this->routes[$this->DEFAULT_HANDLER_KEY] = new Route(BuiltinNotFoundHandler::class);
        } else {
            $this->routes[$this->DEFAULT_HANDLER_KEY] = new Route($default_handler);
        }
    }

    /**
     * @param string $verb
     * @param string $route
     * @param ?class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    private function register(string $verb, string $route, string $handler, array $middlewares): void
    {
        if (array_key_exists($route, $this->routes[$verb])) {
            throw new RouteAlreadyRegisteredException("Route $verb $route already registered");
        }

        if (str_contains($route, "{") || str_contains($route, "}")) {
            // Validate curly braces if necessary
            $open = false;
            foreach (explode($route, "") as $char) {
                if (!$open) {
                    if ($char == "}") {
                        throw new UnmatchedCurlyBraceException("Mismatched wildcard delimiter in route: $verb $route");
                    }

                    if ($char == "{") {
                        $open = true;
                    }
                } else {
                    if ($char == "{") {
                        throw new UnmatchedCurlyBraceException("Mismatched wildcard delimiter in route: $verb $route");
                    }

                    if ($char == "}") {
                        $open = false;
                    }
                }
            }

            // Validate unique wildcards
            $wildcards = array_filter(explode("/", $route), function ($v) {
                return str_contains($v, "{");
            });

            if (count($wildcards) != count(array_unique($wildcards))) {
                throw new DuplicateWildcareException("Duplicate wildcard, make sure the following are all unique: " . implode(",", $wildcards));
            }
        }


        $this->routes[$verb][$route] = new Route($handler, $middlewares);
    }


    private function findAndPrepareMatch(string $verb, string $uri): ?callable
    {
        if (!array_key_exists($verb, $this->routes)) {
            return null;
        }

        $match = array_reduce(array_keys($this->routes[$verb]), function ($match, $candidate) use ($uri, $verb) {
            // If the match has been found, continue
            if ($match != null) {
                return $match;
            }

            // Handle wildcard matching
            $candidate_parts = array_values(array_filter(explode("/", $candidate), function ($v) {
                return $v != "";
            }));

            $uri_parts = array_values(array_filter(explode("/", $uri), function ($v) {
                return $v != "";
            }));

            // If the parts are not the same length, it cannot be satisfied
            if (count($uri_parts) != count($candidate_parts)) {
                return null;
            }

            // If all the parts are satisfied, then the route is satisfied
            $i = 0;
            $result = array_reduce($candidate_parts, function ($acc, $part) use (&$i, $uri_parts) {
                $uri_part = $uri_parts[$i];
                $i++;

                if ($acc == null) {
                    return $acc;
                }

                if (str_starts_with($part, "{")) {
                    $acc['args'][trim($part, "{}")] = $uri_part;
                    return $acc;
                } else {
                    return $part == $uri_part ? $acc : null;
                }
            }, ['args' => []]);

            if ($result == null) {
                return null;
            } else {
                $callback = $this->routes[$verb][$candidate];
                return function () use ($callback, $result) {
                    $callback($result['args']);
                };
            }
        }, null);

        if ($match != null) {
            return $match;
        }

        return function () {
            $this->routes[$this->DEFAULT_HANDLER_KEY]([]);
        };
    }

    public function run(): void
    {
        $verb = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $this->findAndPrepareMatch($verb, $uri)();
    }

    /**
     * Http verb stubs below which delegate to register
     */

    /**
     * @param string $route
     * @param class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    public function GET(string $route, string $handler, array $middlewares = []): void
    {
        $this->register('GET', $route, $handler, $middlewares);
    }

    /**
     * @param string $route
     * @param class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    public function HEAD(string $route, string $handler, array $middlewares = []): void
    {
        $this->register('HEAD', $route, $handler, $middlewares);
    }

    /**
     * @param string $route
     * @param class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    public function POST(string $route, string $handler, array $middlewares = []): void
    {
        $this->register('POST', $route, $handler, $middlewares);
    }

    /**
     * @param string $route
     * @param class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    public function PUT(string $route, string $handler, array $middlewares = []): void
    {
        $this->register('PUT', $route, $handler, $middlewares);
    }

    /**
     * @param string $route
     * @param class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    public function DELETE(string $route, string $handler, array $middlewares = []): void
    {
        $this->register('DELETE', $route, $handler, $middlewares);
    }

    /**
     * @param string $route
     * @param class-string<Handler> $handler
     * @param array<class-string<Middleware>> $middlewares
     */
    public function PATCH(string $route, string $handler, array $middlewares = []): void
    {
        $this->register('PATCH', $route, $handler, $middlewares);
    }
}
