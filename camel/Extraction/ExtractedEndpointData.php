<?php

namespace Knuckles\Camel\Extraction;

use Illuminate\Routing\Route;
use Knuckles\Camel\BaseDTO;
use Knuckles\Scribe\Extracting\Shared\UrlParamsNormalizer;
use Knuckles\Scribe\Tools\Globals;
use ReflectionClass;

class ExtractedEndpointData extends BaseDTO
{
    public array $httpMethods = [];
    public string $uri = '';
    public Metadata $metadata;
    public array $headers = [];
    public array $urlParameters = [];
    public array $cleanUrlParameters = [];
    public array $queryParameters = [];
    public array $cleanQueryParameters = [];
    public array $bodyParameters = [];
    public array $cleanBodyParameters = [];
    public array $fileParameters = [];
    public ResponseCollection $responses;
    public array $responseFields = [];
    public array $auth = [];
    public ?ReflectionClass $controller = null;
    public ?\ReflectionFunctionAbstract $method = null;
    public ?Route $route = null;

    public static function fromRoute(Route $route): static
    {
        $instance = new static([]);
        $instance->httpMethods = $route->methods();
        $instance->uri = $route->uri();
        
        if ($route->getAction('uses') instanceof \Closure) {
            // Handle closure routes
            $instance->controller = null;
            $instance->method = null;
        } else {
            // Handle controller routes
            $controller = $route->getController();
            $class = new ReflectionClass($controller);
            $method = $class->getMethod($route->getActionMethod());
            $instance->controller = $class;
            $instance->method = $method;
        }
        
        $instance->route = $route;
        $instance->metadata = new Metadata([]);
        $instance->responses = new ResponseCollection([]);

        if ($route && $instance->method) {
            $defaultNormalizer = fn() => UrlParamsNormalizer::normalizeParameterNamesInRouteUri($route, $instance->method);
            $instance->uri = match (is_callable(Globals::$__normalizeEndpointUrlUsing)) {
                true => call_user_func_array(Globals::$__normalizeEndpointUrlUsing,
                    [$route->uri, $route, $instance->method, $instance->controller, $defaultNormalizer]),
                default => $defaultNormalizer(),
            };
        }

        return $instance;
    }
}
