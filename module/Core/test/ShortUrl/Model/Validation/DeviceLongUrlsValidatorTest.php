<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Core\ShortUrl\Model\Validation;

use Laminas\Validator\NotEmpty;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Core\Model\DeviceType;
use Shlinkio\Shlink\Core\ShortUrl\Model\Validation\DeviceLongUrlsValidator;
use stdClass;

class DeviceLongUrlsValidatorTest extends TestCase
{
    private DeviceLongUrlsValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new DeviceLongUrlsValidator(new NotEmpty());
    }

    #[Test, DataProvider('provideNonArrayValues')]
    public function nonArrayValuesAreNotValid(mixed $invalidValue): void
    {
        self::assertFalse($this->validator->isValid($invalidValue));
        self::assertEquals(['NOT_ARRAY' => 'Provided value is not an array.'], $this->validator->getMessages());
    }

    public static function provideNonArrayValues(): iterable
    {
        yield 'int' => [0];
        yield 'float' => [100.45];
        yield 'string' => ['foo'];
        yield 'boolean' => [true];
        yield 'object' => [new stdClass()];
        yield 'null' => [null];
    }

    #[Test]
    public function unrecognizedKeysAreNotValid(): void
    {
        self::assertFalse($this->validator->isValid(['foo' => 'bar']));
        self::assertEquals(
            ['INVALID_DEVICE' => 'You have provided at least one invalid device identifier.'],
            $this->validator->getMessages(),
        );
    }

    #[Test]
    public function everyUrlMustMatchLongUrlValidator(): void
    {
        self::assertFalse($this->validator->isValid([DeviceType::ANDROID->value => '']));
        self::assertEquals(
            ['INVALID_LONG_URL' => 'At least one of the long URLs are invalid.'],
            $this->validator->getMessages(),
        );
    }

    #[Test]
    public function validValuesResultInValidResult(): void
    {
        self::assertTrue($this->validator->isValid([
            DeviceType::ANDROID->value => 'foo',
            DeviceType::IOS->value => 'bar',
            DeviceType::DESKTOP->value => 'baz',
        ]));
    }
}
