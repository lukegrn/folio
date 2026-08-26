<?php

declare(strict_types=1);

namespace App\Framework\Router;

use App\Framework\Router\Exceptions\DuplicateWildcardException;
use App\Framework\Router\Exceptions\DuplicateWildcareException;
use App\Framework\Router\Exceptions\RouteAlreadyRegisteredException;
use App\Framework\Router\Exceptions\UnmatchedCurlyBraceException;

use function pcov\waiting;

class Router
{
    private array $routes;
    private string $DEFAULT_HANDLER_KEY = "__default_handler";

    /**
     * @param ?callable() $default_handler
     */
    public function __construct(?callable $default_handler = null)
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
            $this->routes[$this->DEFAULT_HANDLER_KEY] = function () {
                echo 'not found';
            };
        } else {
            $this->routes[$this->DEFAULT_HANDLER_KEY] = $default_handler;
        }
    }

    /**
     * @param string $route
     * @param callable() $handler
     */
    private function register(string $verb, string $route, callable $handler): void
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


        $this->routes[$verb][$route] = $handler;
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

        return $match;
    }

    public function run(): void
    {
        $verb = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $handler = $this->findAndPrepareMatch($verb, $uri) ??
            $this->routes[$this->DEFAULT_HANDLER_KEY];

        $handler();
    }

    /**
     * Http verb stubs below which delegate to register
     */

    /**
     * @param string $route
     * @param callable() $handler
     */
    public function GET(string $route, callable $handler): void
    {
        $this->register('GET', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable() $handler
     */
    public function HEAD(string $route, callable $handler): void
    {
        $this->register('HEAD', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable() $handler
     */
    public function POST(string $route, callable $handler): void
    {
        $this->register('POST', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable() $handler
     */
    public function PUT(string $route, callable $handler): void
    {
        $this->register('PUT', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable() $handler
     */
    public function DELETE(string $route, callable $handler): void
    {
        $this->register('DELETE', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable() $handler
     */
    public function PATCH(string $route, callable $handler): void
    {
        $this->register('PATCH', $route, $handler);
    }
}
