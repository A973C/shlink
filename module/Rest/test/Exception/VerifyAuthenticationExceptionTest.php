<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Rest\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Rest\Exception\VerifyAuthenticationException;

class VerifyAuthenticationExceptionTest extends TestCase
{
    #[Test]
    public function createsExpectedExceptionForInvalidApiKey(): void
    {
        $e = VerifyAuthenticationException::forInvalidApiKey();

        self::assertEquals('Provided API key does not exist or is invalid.', $e->getMessage());
    }
}
