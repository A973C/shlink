<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Rest;

use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Rest\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    private ConfigProvider $configProvider;

    protected function setUp(): void
    {
        $this->configProvider = new ConfigProvider();
    }

    #[Test]
    public function properConfigIsReturned(): void
    {
        $config = ($this->configProvider)();

        self::assertCount(5, $config);
        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey('auth', $config);
        self::assertArrayHasKey('entity_manager', $config);
        self::assertArrayHasKey('access_logs', $config);
        self::assertArrayHasKey(ConfigAbstractFactory::class, $config);
    }

    #[Test, DataProvider('provideRoutesConfig')]
    public function routesAreProperlyPrefixed(array $routes, array $expected): void
    {
        self::assertEquals($expected, ConfigProvider::applyRoutesPrefix($routes));
    }

    public static function provideRoutesConfig(): iterable
    {
        yield 'health action present' => [
            [
                ['path' => '/foo'],
                ['path' => '/bar'],
                ['path' => '/baz/foo'],
                ['path' => '/health'],
            ],
            [
                ['path' => '/rest/v{version:1|2|3}/foo'],
                ['path' => '/rest/v{version:1|2|3}/bar'],
                ['path' => '/rest/v{version:1|2|3}/baz/foo'],
                ['path' => '/rest/v{version:1|2|3}/health'],
                ['path' => '/rest/health', 'name' => ConfigProvider::UNVERSIONED_HEALTH_ENDPOINT_NAME],
            ],
        ];
        yield 'health action not present' => [
            [
                ['path' => '/foo'],
                ['path' => '/bar'],
                ['path' => '/baz/foo'],
            ],
            [
                ['path' => '/rest/v{version:1|2|3}/foo'],
                ['path' => '/rest/v{version:1|2|3}/bar'],
                ['path' => '/rest/v{version:1|2|3}/baz/foo'],
            ],
        ];
    }
}
