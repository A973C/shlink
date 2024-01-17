<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Rest\ApiKey\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Core\Domain\Entity\Domain;
use Shlinkio\Shlink\Rest\ApiKey\Model\RoleDefinition;
use Shlinkio\Shlink\Rest\ApiKey\Role;

class RoleDefinitionTest extends TestCase
{
    #[Test]
    public function forAuthoredShortUrlsCreatesRoleDefinitionAsExpected(): void
    {
        $definition = RoleDefinition::forAuthoredShortUrls();

        self::assertEquals(Role::AUTHORED_SHORT_URLS, $definition->role);
        self::assertEquals([], $definition->meta);
    }

    #[Test]
    public function forDomainCreatesRoleDefinitionAsExpected(): void
    {
        $domain = Domain::withAuthority('foo.com');
        $domain->setId('123');
        $definition = RoleDefinition::forDomain($domain);

        self::assertEquals(Role::DOMAIN_SPECIFIC, $definition->role);
        self::assertEquals(['domain_id' => '123', 'authority' => 'foo.com'], $definition->meta);
    }
}
