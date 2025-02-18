<?php

namespace Knuckles\Scribe\Config;

use Illuminate\Support\Arr;

class Output
{
    public static function with(
        array   $type = [],
        ?string $title = null,
        string  $description = '',
        array   $baseUrls = [],
        array   $exampleLanguages = ['bash', 'javascript'],
        bool    $logo = false,
        string  $lastUpdated = 'Last updated: {date:F j, Y}',
        array   $postman = ['enabled' => true],
        array   $openApi = ['enabled' => true],
        array   $tryItOut = ['enabled' => true],
        array   $groupsOrder = [],
        string  $introText = "",
    ): static
    {
        return new static(...get_defined_vars());
    }

    public function __construct(
        public ?string $title,
        public string  $description,
        public array   $baseUrls, /* If empty, Scribe will use config('app.url') */
        public array   $groupsOrder,
        public string  $introText,
        public array   $exampleLanguages,
        public bool    $logo,
        public string  $lastUpdated,

        public array   $type,
        public array   $postman,
        public array   $openApi,
        public array   $tryItOut,
    )
    {
    }

    public static function staticType(
        InHouseTheme $theme = InHouseTheme::Elements,
        string       $outputPath = 'public/docs',
    ): array
    {
        return [
            'type' => 'static',
            'theme' => $theme->value,
            'extra' => [
                'static.output_path' => $outputPath,
            ],
        ];
    }

    public static function externalStaticType(
        ExternalTheme $theme = ExternalTheme::Scalar,
        string        $outputPath = 'public/docs',
        array         $htmlAttributes = [],
    ): array
    {
        return [
            'type' => 'external_static',
            'theme' => $theme->value,
            'extra' => [
                'static.output_path' => $outputPath,
                'external.html_attributes' => $htmlAttributes,
            ],
        ];
    }

    public static function laravelType(
        InHouseTheme $theme = InHouseTheme::Elements,
        bool         $addRoutes = true,
        string       $docsUrl = '/docs',
        array        $middleware = [],
        ?string      $assetsDirectory = null,
    ): array
    {
        return [
            'type' => 'laravel',
            'theme' => $theme->value,
            'extra' => [
                'laravel.add_routes' => $addRoutes,
                'laravel.docs_url' => $docsUrl,
                'laravel.assets_directory' => $assetsDirectory,
                'laravel.middleware' => $middleware,
            ],
        ];
    }

    public static function externalLaravelType(
        ExternalTheme $theme = ExternalTheme::Scalar,
        bool          $addRoutes = true,
        string        $docsUrl = '/docs',
        array         $middleware = [],
        array         $htmlAttributes = [],
        ?string       $assetsDirectory = null,
    ): array
    {
        return [
            'type' => 'external_laravel',
            'theme' => $theme->value,
            'extra' => [
                'laravel.add_routes' => $addRoutes,
                'laravel.docs_url' => $docsUrl,
                'laravel.assets_directory' => $assetsDirectory,
                'laravel.middleware' => $middleware,
                'external.html_attributes' => $htmlAttributes,
            ],
        ];
    }

    public static function postman(
        bool  $enabled = true,
        array $overrides = [],
    ): array
    {
        return get_defined_vars();
    }

    public static function openApi(
        bool  $enabled = true,
        array $overrides = [],
        array $generators = [],
    ): array
    {
        return get_defined_vars();
    }

    public static function tryItOut(
        bool    $enabled = true,
        ?string $baseUrl = null,
        bool    $useCsrf = false,
        string  $csrfUrl = '/sanctum/csrf-cookie',
    ): array
    {
        return get_defined_vars();
    }

    public function serializeInto(array $config)
    {
        $core = [
            'title' => $this->title,
            'description' => $this->description,
            'intro_text' => $this->introText,
            'example_languages' => $this->exampleLanguages,
            'logo' => $this->logo,
            'last_updated' => $this->lastUpdated,
            'base_url' => Arr::first($this->baseUrls) ?? null,
            'type' => $this->type['type'],
            'theme' => $this->type['theme'],
            'try_it_out' => Serializer::translateKeys($this->tryItOut),
            'postman' => Serializer::translateKeys($this->postman),
            'openapi' => Serializer::translateKeys($this->openApi),
            'groups.order' => $this->groupsOrder,
            ...$this->type['extra']
        ];
        foreach ($core as $key => $value) {
            data_set($config, $key, $value);
        }

        return $config;
    }
}

enum ExternalTheme: string
{
    case Elements = 'elements';
    case Scalar = 'scalar';
    case Rapidoc = 'rapidoc';
}

enum InHouseTheme: string
{
    case Elements = 'elements';
    case Legacy = 'default';
}
