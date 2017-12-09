<?php

namespace Illuminate\Support\Facades;

/**
 * @method static void get(string $uri, \Closure|array|string $action)
 * @method static void post(string $uri, \Closure|array|string $action)
 * @method static void put(string $uri, \Closure|array|string $action)
 * @method static void delete(string $uri, \Closure|array|string $action)
 * @method static void patch(string $uri, \Closure|array|string $action)
 * @method static void options(string $uri, \Closure|array|string $action)
 * @method static void match(array|string $methods, string $uri, \Closure|array|string $action)
 * @method static void resource(string $name, string $controller, array $options = [])
 * @method static void group(array $attributes, \Closure $callback)
 * @method static \Illuminate\Routing\Route substituteBindings(\Illuminate\Routing\Route $route)
 * @method static void substituteImplicitBindings(\Illuminate\Routing\Route $route)
 *
 * @see \Illuminate\Routing\Router
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}
