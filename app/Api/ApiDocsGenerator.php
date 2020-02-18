<?php namespace BookStack\Api;

use BookStack\Http\Controllers\Api\ApiController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ApiDocsGenerator
{

    protected $reflectionClasses = [];
    protected $controllerClasses = [];

    /**
     * Generate API documentation.
     */
    public function generate(): Collection
    {
        $apiRoutes = $this->getFlatApiRoutes();
        $apiRoutes = $this->loadDetailsFromControllers($apiRoutes);
        $apiRoutes = $this->loadDetailsFromFiles($apiRoutes);
        $apiRoutes = $apiRoutes->groupBy('base_model');
        return $apiRoutes;
    }

    /**
     * Load any API details stored in static files.
     */
    protected function loadDetailsFromFiles(Collection $routes): Collection
    {
        return $routes->map(function (array $route) {
            $exampleTypes = ['request', 'response'];
            foreach ($exampleTypes as $exampleType) {
                $exampleFile = base_path("dev/api/{$exampleType}s/{$route['name']}.json");
                $exampleContent = file_exists($exampleFile) ? file_get_contents($exampleFile) : null;
                $route["example_{$exampleType}"] = $exampleContent;
            }
            return $route;
        });
    }

    /**
     * Load any details we can fetch from the controller and its methods.
     */
    protected function loadDetailsFromControllers(Collection $routes): Collection
    {
        return $routes->map(function (array $route) {
            $method = $this->getReflectionMethod($route['controller'], $route['controller_method']);
            $comment = $method->getDocComment();
            $route['description'] = $comment ? $this->parseDescriptionFromMethodComment($comment) : null;
            $route['body_params'] = $this->getBodyParamsFromClass($route['controller'], $route['controller_method']);
            return $route;
        });
    }

    /**
     * Load body params and their rules by inspecting the given class and method name.
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getBodyParamsFromClass(string $className, string $methodName): ?array
    {
        /** @var ApiController $class */
        $class = $this->controllerClasses[$className] ?? null;
        if ($class === null) {
            $class = app()->make($className);
            $this->controllerClasses[$className] = $class;
        }

        $rules = $class->getValdationRules()[$methodName] ?? [];
        foreach ($rules as $param => $ruleString) {
            $rules[$param] = explode('|', $ruleString);
        }
        return count($rules) > 0 ? $rules : null;
    }

    /**
     * Parse out the description text from a class method comment.
     */
    protected function parseDescriptionFromMethodComment(string $comment)
    {
        $matches = [];
        preg_match_all('/^\s*?\*\s((?![@\s]).*?)$/m', $comment, $matches);
        return implode(' ', $matches[1] ?? []);
    }

    /**
     * Get a reflection method from the given class name and method name.
     * @throws ReflectionException
     */
    protected function getReflectionMethod(string $className, string $methodName): ReflectionMethod
    {
        $class = $this->reflectionClasses[$className] ?? null;
        if ($class === null) {
            $class = new ReflectionClass($className);
            $this->reflectionClasses[$className] = $class;
        }

        return $class->getMethod($methodName);
    }

    /**
     * Get the system API routes, formatted into a flat collection.
     */
    protected function getFlatApiRoutes(): Collection
    {
        return collect(Route::getRoutes()->getRoutes())->filter(function ($route) {
            return strpos($route->uri, 'api/') === 0;
        })->map(function ($route) {
            [$controller, $controllerMethod] = explode('@', $route->action['uses']);
            $baseModelName = explode('.', explode('/', $route->uri)[1])[0];
            $shortName = $baseModelName . '-' . $controllerMethod;
            return [
                'name' => $shortName,
                'uri' => $route->uri,
                'method' => $route->methods[0],
                'controller' => $controller,
                'controller_method' => $controllerMethod,
                'base_model' => $baseModelName,
            ];
        });
    }

}