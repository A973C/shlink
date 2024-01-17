<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Rest\Action\Visit;

use Cake\Chronos\Chronos;
use Laminas\Diactoros\ServerRequestFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Common\Paginator\Paginator;
use Shlinkio\Shlink\Common\Util\DateRange;
use Shlinkio\Shlink\Core\ShortUrl\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\Core\Visit\Model\VisitsParams;
use Shlinkio\Shlink\Core\Visit\VisitsStatsHelperInterface;
use Shlinkio\Shlink\Rest\Action\Visit\ShortUrlVisitsAction;
use Shlinkio\Shlink\Rest\Entity\ApiKey;

class ShortUrlVisitsActionTest extends TestCase
{
    private ShortUrlVisitsAction $action;
    private MockObject & VisitsStatsHelperInterface $visitsHelper;

    protected function setUp(): void
    {
        $this->visitsHelper = $this->createMock(VisitsStatsHelperInterface::class);
        $this->action = new ShortUrlVisitsAction($this->visitsHelper);
    }

    #[Test]
    public function providingCorrectShortCodeReturnsVisits(): void
    {
        $shortCode = 'abc123';
        $this->visitsHelper->expects($this->once())->method('visitsForShortUrl')->with(
            ShortUrlIdentifier::fromShortCodeAndDomain($shortCode),
            $this->isInstanceOf(VisitsParams::class),
            $this->isInstanceOf(ApiKey::class),
        )->willReturn(new Paginator(new ArrayAdapter([])));

        $response = $this->action->handle($this->requestWithApiKey()->withAttribute('shortCode', $shortCode));
        self::assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function paramsAreReadFromQuery(): void
    {
        $shortCode = 'abc123';
        $this->visitsHelper->expects($this->once())->method('visitsForShortUrl')->with(
            ShortUrlIdentifier::fromShortCodeAndDomain($shortCode),
            new VisitsParams(
                DateRange::until(Chronos::parse('2016-01-01 00:00:00')),
                3,
                10,
            ),
            $this->isInstanceOf(ApiKey::class),
        )->willReturn(new Paginator(new ArrayAdapter([])));

        $response = $this->action->handle(
            $this->requestWithApiKey()->withAttribute('shortCode', $shortCode)
                                      ->withQueryParams([
                                          'endDate' => '2016-01-01 00:00:00',
                                          'page' => '3',
                                          'itemsPerPage' => '10',
                                      ]),
        );
        self::assertEquals(200, $response->getStatusCode());
    }

    private function requestWithApiKey(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals()->withAttribute(ApiKey::class, ApiKey::create());
    }
}
