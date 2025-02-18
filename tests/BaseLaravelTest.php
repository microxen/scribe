<?php

namespace Knuckles\Scribe\Tests;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\ScribeServiceProvider;
use Orchestra\Testbench\TestCase;
use function Knuckles\Scribe\Config\configureStrategy;
use Knuckles\Scribe\Config;
use Knuckles\Scribe\Extracting\Strategies;

class BaseLaravelTest extends TestCase
{
    use TestHelpers;
    use ArraySubsetAsserts;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        ScribeServiceProvider::$customTranslationLayerLoaded = false;
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        $providers = [
            ScribeServiceProvider::class,
        ];
        return $providers;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setConfig(Config\Factory::make(
            extracting: Config\Extracting::with(
                routes: Config\Routes::match(prefixes: ['*']),
                defaultGroup: 'Endpoints',
                databaseConnectionsToTransact: [],
                fakerSeedForExamples: 1234,
                auth: Config\Extracting::auth(
                    enabled: false,
                    default: false,
                    in: AuthIn::BEARER,
                    useValue: env('SCRIBE_AUTH_KEY'),
                    placeholder: '{YOUR_AUTH_KEY}',
                ),
                strategies: Config\Extracting::strategies(
                    metadata: Config\Defaults::METADATA_STRATEGIES,
                    urlParameters: Config\Defaults::URL_PARAMETERS_STRATEGIES,
                    queryParameters: Config\Defaults::QUERY_PARAMETERS_STRATEGIES,
                    headers: Config\Defaults::HEADERS_STRATEGIES,
                    bodyParameters: Config\Defaults::BODY_PARAMETERS_STRATEGIES,
                    responses: configureStrategy(
                        Config\Defaults::RESPONSES_STRATEGIES,
                        Strategies\Responses\ResponseCalls::withSettings(
                            only: [],
                            except: ['*'], // Disabled to speed up tests
                            config: [
                                'app.env' => 'documentation',
                                // 'app.debug' => false,
                            ],
                            queryParams: [],
                            bodyParams: [],
                            fileParams: [],
                            cookies: [],
                        )),
                    responseFields: Config\Defaults::RESPONSE_FIELDS_STRATEGIES,
                )
            ),
            output: Config\Output::with(
                type: Config\Output::laravelType(
                    theme: Config\InHouseTheme::Legacy,
                    docsUrl: '/apidocs'
                ),
                baseUrls: [
                    "production" => config("app.base_url"),
                ],
                // Skip these for faster tests
                postman: Config\Output::postman(
                    enabled: false,
                ),
                openApi: Config\Output::openApi(
                    enabled: false,
                ),
            )
        ));
    }

    protected function setConfig($configValues): void
    {
        foreach ($configValues as $key => $value) {
            config(["scribe.$key" => $value]);
        }
    }
}
