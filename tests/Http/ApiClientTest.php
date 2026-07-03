<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Http;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Resellerclub\Config\Credentials;
use Resellerclub\Config\Environment;
use Resellerclub\Exception\ApiConnectionException;
use Resellerclub\Http\ApiClient;
use Resellerclub\Http\HttpMethod;
use Resellerclub\Tests\Support\MockTransport;

#[CoversClass(ApiClient::class)]
final class ApiClientTest extends TestCase
{
  public function testGetRequestBuildsUrlWithAuthAndParameters(): void
  {
    $transport = new MockTransport();
    $transport->willReturn('{"example.com":{"status":"available"}}');

    $result = $transport->apiClient->send(
      HttpMethod::Get,
      'domains',
      'available',
      ['domain-name' => 'example', 'tlds' => 'com'],
    );

    $request = $transport->lastRequest();
    self::assertSame('GET', $request->getMethod());
    self::assertSame(
      'https://test.httpapi.com/api/domains/available.json'
      . '?auth-userid=AUTHUSER&api-key=APIKEY&domain-name=example&tlds=com',
      (string) $request->getUri(),
    );
    self::assertSame('', (string) $request->getBody());
    self::assertSame(['example.com' => ['status' => 'available']], $result);
  }

  public function testPostRequestSendsParametersInBody(): void
  {
    $transport = new MockTransport();
    $transport->willReturn();

    $transport->apiClient->send(
      HttpMethod::Post,
      'domains',
      'register',
      ['domain-name' => 'example.com', 'years' => 1],
    );

    $request = $transport->lastRequest();
    self::assertSame('POST', $request->getMethod());
    self::assertSame('https://test.httpapi.com/api/domains/register.json', (string) $request->getUri());
    self::assertSame('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
    self::assertSame(
      'auth-userid=AUTHUSER&api-key=APIKEY&domain-name=example.com&years=1',
      (string) $request->getBody(),
    );
  }

  public function testSection2IsInsertedIntoThePath(): void
  {
    $transport = new MockTransport();
    $transport->willReturn();

    $transport->apiClient->send(HttpMethod::Get, 'domains', 'suggest-names', ['keyword' => 'cloud'], 'v5');

    self::assertStringStartsWith(
      'https://test.httpapi.com/api/domains/v5/suggest-names.json?',
      (string) $transport->lastRequest()->getUri(),
    );
  }

  public function testUsesProductionHostFromCredentials(): void
  {
    $transport = new MockTransport(
      Environment::Production,
      new Credentials('AUTHUSER', 'APIKEY', Environment::Production),
    );
    $transport->willReturn();

    $transport->apiClient->send(HttpMethod::Get, 'country', 'list');

    self::assertStringStartsWith(
      'https://httpapi.com/api/country/list.json',
      (string) $transport->lastRequest()->getUri(),
    );
  }

  public function testThrowsApiConnectionExceptionOnTransportFailure(): void
  {
    $transport = new MockTransport();
    $transport->willThrow(new ConnectException('boom', new Request('GET', 'test')));

    $this->expectException(ApiConnectionException::class);

    $transport->apiClient->send(HttpMethod::Get, 'domains', 'available');
  }

  public function testThrowsApiConnectionExceptionOnInvalidJson(): void
  {
    $transport = new MockTransport();
    $transport->willReturn('<html>not json</html>');

    $this->expectException(ApiConnectionException::class);

    $transport->apiClient->send(HttpMethod::Get, 'domains', 'available');
  }
}
