<?php

use Knuckles\Scribe\Config;
use Knuckles\Scribe\Config\{AuthIn,ExternalTheme};
use Knuckles\Scribe\Extracting\Strategies;
use function Knuckles\Scribe\Config\{removeStrategies, configureStrategy};

/**
 * For documentation, use your IDE's autocomplete features, or see https://scribe.knuckles.wtf/laravel/reference/config
 */

return Config\Factory::make(
    extracting: Config\Extracting::with(
        routes: Config\Routes::match(
            prefixes: ['api/*'],
            domains: ['*'],
            alwaysInclude: [],
            alwaysExclude: [],
        ),
        defaultGroup: 'Endpoints',
        databaseConnectionsToTransact: [config('database.default')],
        fakerSeedForExamples: 1234,
        dataSourcesForExampleModels: ['factoryCreate', 'factoryMake', 'databaseFirst'],
        auth: Config\Extracting::auth(
            enabled: false,
            default: false,
            in: AuthIn::BEARER,
            useValue: env('SCRIBE_AUTH_KEY'),
            placeholder: '{YOUR_AUTH_KEY}',
            extraInfo: <<<MARKDOWN
              You can retrieve your token by visiting your dashboard and clicking <b>Generate API token</b>.
            MARKDOWN
        ),
        strategies: Config\Extracting::strategies(
        // The strategies Scribe will use to extract information about your routes at each stage.
        // Use configureStrategy() to specify settings for a strategy in the list.
        // Use removeStrategies() to remove an included strategy.
            metadata: [...Config\Defaults::METADATA_STRATEGIES],
            urlParameters: [...Config\Defaults::URL_PARAMETERS_STRATEGIES],
            queryParameters: [...Config\Defaults::QUERY_PARAMETERS_STRATEGIES],
            headers: [
                ...Config\Defaults::HEADERS_STRATEGIES,
                Strategies\StaticData::withSettings(data: [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]),
            ],
            bodyParameters: [...Config\Defaults::BODY_PARAMETERS_STRATEGIES],
            responses: configureStrategy(
                Config\Defaults::RESPONSES_STRATEGIES,
                Strategies\Responses\ResponseCalls::withSettings(
                    only: ['GET *'],
                    config: [
                        'app.env' => 'documentation',
                        // 'app.debug' => false,
                    ],
                    queryParams: [],
                    bodyParams: [],
                    fileParams: [],
                    cookies: [],
                )),
            responseFields: [...Config\Defaults::RESPONSE_FIELDS_STRATEGIES],
        )
    ),
    output: Config\Output::with(
        type: Config\Output::externalLaravelType(
            theme: ExternalTheme::Scalar,
            docsUrl: '/docs',
        ),
        title: config('app.name').' API Documentation',
        description: '',
        baseUrls: [
            "production" => config("app.url"),
        ],
        exampleLanguages: ['bash', 'javascript'],
        logo: false,
        lastUpdated: 'Last updated: {date:F j, Y}',
        postman: Config\Output::postman(
            enabled: true,
            overrides: [
                // 'info.version' => '2.0.0',
            ]
        ),
        openApi: Config\Output::openApi(
            enabled: true,
            overrides: [
                // 'info.version' => '2.0.0',
            ],
            generators: [],
        ),
        tryItOut: Config\Output::tryItOut(
            enabled: true,
        ),
        groupsOrder: [
            // 'This group will come first',
            // 'This group will come next' => [
            //     'POST /this-endpoint-will-come-first',
            //     'GET /this-endpoint-will-come-next',
            // ],
            // 'This group will come third' => [
            //     'This subgroup will come first' => [
            //         'GET /this-other-endpoint-will-come-first',
            //     ]
            // ]
        ],
        introText: <<<MARKDOWN
          This documentation aims to provide all the information you need to work with our API.

          <aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
          You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>
        MARKDOWN,
    )
);
