<?php

namespace Knuckles\Scribe\Config;

use Illuminate\Support\Str;

class Serializer
{
    public static function toOldConfig(Extracting $extractingConfig, Output $outputConfig): array
    {
        $config = ['__configVersion' => 'v2'];
        $config = $extractingConfig->serializeInto($config);
        $config = $outputConfig->serializeInto($config);
        return $config;
    }

    public static function generateRoutesConfig(Routes $routesConfig): array
    {
        return [
            [
                'match' => [
                    'prefixes' => $routesConfig->prefixes,
                    'domains' => $routesConfig->domains,
                ],
                'include' => $routesConfig->alwaysInclude,
                'exclude' => $routesConfig->alwaysExclude,
            ]
        ];
    }

    public static function translateKeys($array)
    {
        return collect($array)->mapWithKeys(function ($value, $key) {
            return [Str::snake($key) => $value];
        })->toArray();
    }
}
