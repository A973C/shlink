<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\CLI\Command\Api;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\CLI\Command\Api\DisableKeyCommand;
use Shlinkio\Shlink\Common\Exception\InvalidArgumentException;
use Shlinkio\Shlink\Rest\Service\ApiKeyServiceInterface;
use ShlinkioTest\Shlink\CLI\Util\CliTestUtils;
use Symfony\Component\Console\Tester\CommandTester;

class DisableKeyCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject & ApiKeyServiceInterface $apiKeyService;

    protected function setUp(): void
    {
        $this->apiKeyService = $this->createMock(ApiKeyServiceInterface::class);
        $this->commandTester = CliTestUtils::testerForCommand(new DisableKeyCommand($this->apiKeyService));
    }

    #[Test]
    public function providedApiKeyIsDisabled(): void
    {
        $apiKey = 'abcd1234';
        $this->apiKeyService->expects($this->once())->method('disable')->with($apiKey);

        $this->commandTester->execute([
            'apiKey' => $apiKey,
        ]);
        $output = $this->commandTester->getDisplay();

        self::assertStringContainsString('API key "abcd1234" properly disabled', $output);
    }

    #[Test]
    public function errorIsReturnedIfServiceThrowsException(): void
    {
        $apiKey = 'abcd1234';
        $expectedMessage = 'API key "abcd1234" does not exist.';
        $this->apiKeyService->expects($this->once())->method('disable')->with($apiKey)->willThrowException(
            new InvalidArgumentException($expectedMessage),
        );

        $this->commandTester->execute([
            'apiKey' => $apiKey,
        ]);
        $output = $this->commandTester->getDisplay();

        self::assertStringContainsString($expectedMessage, $output);
    }
}
