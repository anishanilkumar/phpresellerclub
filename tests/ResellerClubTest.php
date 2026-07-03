<?php

declare(strict_types=1);

namespace Resellerclub\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Resellerclub\Api\Billing;
use Resellerclub\Api\Contact;
use Resellerclub\Api\Customer;
use Resellerclub\Api\Domain;
use Resellerclub\Config\Credentials;
use Resellerclub\Config\Environment;
use Resellerclub\ResellerClub;

#[CoversClass(ResellerClub::class)]
final class ResellerClubTest extends TestCase
{
    public function testWiresTheWholeStackTogether(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"example.com":{"status":"available"}}'),
        ]);
        $guzzle = new GuzzleClient(['handler' => HandlerStack::create($mock)]);
        $factory = new HttpFactory();

        $rc = new ResellerClub(
            new Credentials('12345678', 'apikey', Environment::Test),
            $guzzle,
            $factory,
            $factory,
        );

        $result = $rc->domains()->checkAvailability('example', 'com');

        $request = $mock->getLastRequest();
        self::assertNotNull($request);
        self::assertSame('GET', $request->getMethod());
        self::assertSame(
            'https://test.httpapi.com/api/domains/available.json'
            . '?auth-userid=12345678&api-key=apikey&domain-name=example&tlds=com&suggest-alternative=false',
            (string) $request->getUri(),
        );
        self::assertSame(['example.com' => ['status' => 'available']], $result);
    }

    public function testAccessorsReturnSharedTypedInstances(): void
    {
        $rc = new ResellerClub(new Credentials('12345678', 'apikey'));

        self::assertInstanceOf(Domain::class, $rc->domains());
        self::assertInstanceOf(Contact::class, $rc->contacts());
        self::assertInstanceOf(Customer::class, $rc->customers());
        self::assertInstanceOf(Billing::class, $rc->billing());

        // Accessors are memoised.
        self::assertSame($rc->domains(), $rc->domains());
    }

    public function testCanBeConstructedWithoutAnHttpClient(): void
    {
        $rc = new ResellerClub(new Credentials('12345678', 'apikey'));

        self::assertInstanceOf(Domain::class, $rc->domains());
    }
}
