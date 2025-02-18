<?php

namespace Knuckles\Scribe\Config;

use Illuminate\Support\Arr;

class Extracting
{
    public static function with(
        Routes  $routes,
        string  $defaultGroup = 'Endpoints',
        array   $databaseConnectionsToTransact = [],
        ?int    $fakerSeedForExamples = 1234,
        array   $dataSourcesForExampleModels = ['factoryCreate', 'factoryMake', 'databaseFirst'],
        array   $auth = [],
        array   $strategies = [],
        ?string $routeMatcher = \Knuckles\Scribe\Matching\RouteMatcher::class,
        ?string $fractalSerializer = null,
    ): Extracting
    {
        return new self(...get_defined_vars());
    }

    public function __construct(
        public Routes  $routes,
        public string  $defaultGroup,
        public array   $databaseConnectionsToTransact,
        public ?int    $fakerSeedForExamples,
        public array   $dataSourcesForExampleModels,
        public array   $auth,
        public array   $strategies,
        public ?string $routeMatcher,
        public ?string $fractalSerializer,
    )
    {
    }

    public static function auth(
        bool    $enabled = true,
        bool    $default = false,
        AuthIn  $in = AuthIn::BEARER,
        string  $name = 'key',
        ?string $useValue = null,
        string  $placeholder = '{YOUR_AUTH_KEY}',
        string  $extraInfo = ''
    ): array
    {
        return array_merge(get_defined_vars(), ['in' => $in->value]);
    }

    public static function strategies(
        array $metadata,
        array $urlParameters,
        array $queryParameters,
        array $headers,
        array $bodyParameters,
        array $responses,
        array $responseFields,
    ): array
    {
        return get_defined_vars();
    }

    public function serializeInto(array $config)
    {
        $core = [
            'examples' => [
                'faker_seed' => $this->fakerSeedForExamples,
                'models_source' => $this->dataSourcesForExampleModels,
            ],
            'routeMatcher' => $this->routeMatcher,
            'database_connections_to_transact' => $this->databaseConnectionsToTransact,
            'fractal' => [
                'serializer' => $this->fractalSerializer,
            ],
            'auth' => Serializer::translateKeys($this->auth),
            'strategies' => $this->strategies,
            'routes' => Serializer::generateRoutesConfig($this->routes),
            'groups.default' => $this->defaultGroup,
        ];

        foreach ($core as $key => $value) {
            data_set($config, $key, $value);
        }
        return $config;
    }
}

enum AuthIn: string
{
    case BEARER = 'bearer';
    case BASIC = 'basic';
    case HEADER = 'header';
    case QUERY = 'query';
    case BODY = 'body';
    case QUERY_OR_BODY = 'query_or_body';
}
