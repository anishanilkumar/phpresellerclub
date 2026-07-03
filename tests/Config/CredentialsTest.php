<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Config;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Resellerclub\Config\Credentials;
use Resellerclub\Config\Environment;

#[CoversClass(Credentials::class)]
final class CredentialsTest extends TestCase
{
    public function testExposesItsValues(): void
    {
        $credentials = new Credentials('12345678', 'secret', Environment::Production);

        self::assertSame('12345678', $credentials->authUserId());
        self::assertSame('secret', $credentials->apiKey());
        self::assertSame(Environment::Production, $credentials->environment());
    }

    public function testDefaultsToTestEnvironment(): void
    {
        self::assertSame(Environment::Test, (new Credentials('id', 'key'))->environment());
    }

    public function testBuildsAuthParameters(): void
    {
        $credentials = new Credentials('12345678', 'secret');

        self::assertSame(
            ['auth-userid' => '12345678', 'api-key' => 'secret'],
            $credentials->toAuthParameters(),
        );
    }
}
