<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Config;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Resellerclub\Config\Environment;

#[CoversClass(Environment::class)]
final class EnvironmentTest extends TestCase
{
    public function testTestEnvironmentHost(): void
    {
        self::assertSame('test.httpapi.com', Environment::Test->host());
    }

    public function testProductionEnvironmentHost(): void
    {
        self::assertSame('httpapi.com', Environment::Production->host());
    }
}
