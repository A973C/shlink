<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Rest\Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Rest\Exception\MissingAuthenticationException;

use function implode;
use function sprintf;

class MissingAuthenticationExceptionTest extends TestCase
{
    #[Test, DataProvider('provideExpectedHeaders')]
    public function exceptionIsProperlyCreatedFromExpectedHeaders(array $expectedHeaders): void
    {
        $expectedMessage = sprintf(
            'Expected one of the following authentication headers, ["%s"], but none were provided',
            implode('", "', $expectedHeaders),
        );

        $e = MissingAuthenticationException::forHeaders($expectedHeaders);

        $this->assertCommonExceptionShape($e);
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedMessage, $e->getDetail());
        self::assertEquals(['expectedHeaders' => $expectedHeaders], $e->getAdditionalData());
    }

    public static function provideExpectedHeaders(): iterable
    {
        yield [['foo', 'bar']];
        yield [['something']];
        yield [[]];
        yield [['foo', 'bar', 'baz']];
    }

    #[Test, DataProvider('provideExpectedParam')]
    public function exceptionIsProperlyCreatedFromExpectedQueryParam(string $param): void
    {
        $expectedMessage = sprintf('Expected authentication to be provided in "%s" query param', $param);

        $e = MissingAuthenticationException::forQueryParam($param);

        $this->assertCommonExceptionShape($e);
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedMessage, $e->getDetail());
        self::assertEquals(['param' => $param], $e->getAdditionalData());
    }

    public static function provideExpectedParam(): iterable
    {
        yield ['foo'];
        yield ['bar'];
        yield ['something'];
    }

    private function assertCommonExceptionShape(MissingAuthenticationException $e): void
    {
        self::assertEquals('Invalid authorization', $e->getTitle());
        self::assertEquals('https://shlink.io/api/error/missing-authentication', $e->getType());
        self::assertEquals(401, $e->getStatus());
    }
}
