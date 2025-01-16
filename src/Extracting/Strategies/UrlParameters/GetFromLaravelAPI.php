<?php

namespace Knuckles\Scribe\Extracting\Strategies\UrlParameters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\ParamHelpers;
use Knuckles\Scribe\Extracting\Shared\UrlParamsNormalizer;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use Knuckles\Scribe\Tools\Utils;
use Throwable;

class GetFromLaravelAPI extends Strategy
{
    use ParamHelpers;

    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): array
    {
        if (!$endpointData->method) {
            return [];
        }

        $parameters = $this->getFromLaravelAPI($endpointData->route);
        return $this->inferBetterTypesAndExamplesForEloquentUrlParameters($parameters, $endpointData);
    }

    protected function inferBetterTypesAndExamplesForEloquentUrlParameters(array $parameters, ExtractedEndpointData $endpointData): array
    {
        if (!$endpointData->method) {
            return $parameters;
        }

        $eloquentModels = UrlParamsNormalizer::getTypeHintedEloquentModels($endpointData->method);
        
        foreach ($parameters as $name => $parameter) {
            if (empty($eloquentModels[$name])) continue;
            
            $model = $eloquentModels[$name];
            $parameters[$name]['type'] = 'integer';
            $parameters[$name]['example'] = $model->getKey();
        }

        return $parameters;
    }

    protected function getFromLaravelAPI(\Illuminate\Routing\Route $route): array
    {
        $parameters = [];
        foreach ($route->parameterNames() as $parameterName) {
            $parameters[$parameterName] = [
                'name' => $parameterName,
                'description' => '',
                'required' => true,
            ];
        }
        return $parameters;
    }
}
