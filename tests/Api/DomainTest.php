<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Resellerclub\Api\AbstractApi;
use Resellerclub\Api\Domain;
use Resellerclub\Tests\Support\MockTransport;
use Resellerclub\Validation\Validator;

#[CoversClass(Domain::class)]
#[CoversClass(AbstractApi::class)]
final class DomainTest extends TestCase
{
  public function testCheckAvailabilityBuildsGetRequest(): void
  {
    $transport = new MockTransport();
    $transport->willReturn('{"example.com":{"status":"available"}}');
    $domain = new Domain($transport->apiClient, new Validator());

    $result = $domain->checkAvailability('example', ['com', 'net'], true);

    self::assertSame(
      'https://test.httpapi.com/api/domains/available.json'
      . '?auth-userid=AUTHUSER&api-key=APIKEY&domain-name=example'
      . '&tlds=com&tlds=net&suggest-alternative=true',
      (string) $transport->lastRequest()->getUri(),
    );
    self::assertSame(['example.com' => ['status' => 'available']], $result);
  }

  public function testCheckAvailabilityIdnConvertsToPunycode(): void
  {
    $transport = new MockTransport();
    $transport->willReturn();
    $domain = new Domain($transport->apiClient, new Validator());

    $domain->checkAvailabilityIdn('münchen', 'com', 'de');

    self::assertStringContainsString(
      'domain-name=xn--mnchen-3ya',
      (string) $transport->lastRequest()->getUri(),
    );
  }

  public function testRegisterSendsPostBody(): void
  {
    $transport = new MockTransport();
    $transport->willReturn();
    $domain = new Domain($transport->apiClient, new Validator());

    $domain->register('example.com', ['years' => 1, 'customer-id' => '13647145']);

    $request = $transport->lastRequest();
    self::assertSame('POST', $request->getMethod());
    self::assertSame('https://test.httpapi.com/api/domains/register.json', (string) $request->getUri());
    self::assertStringContainsString('domain-name=example.com', (string) $request->getBody());
    self::assertStringContainsString('years=1', (string) $request->getBody());
  }

  /**
   * @param callable(Domain): mixed $call
   */
  #[DataProvider('callProvider')]
  public function testIssuesExpectedRequest(callable $call, string $method, string $pathContains): void
  {
    $transport = new MockTransport();
    $transport->willReturn();
    $domain = new Domain($transport->apiClient, new Validator());

    $call($domain);

    $request = $transport->lastRequest();
    self::assertSame($method, $request->getMethod());
    self::assertStringContainsString($pathContains, (string) $request->getUri());
  }

  /**
   * @return iterable<string, array{callable(Domain): mixed, string, string}>
   */
  public static function callProvider(): iterable
  {
    yield 'checkAvailabilityPremium' => [
      fn (Domain $d) => $d->checkAvailabilityPremium('cloud', 'com', ['price-high' => 1000]),
      'GET', '/api/domains/premium/available.json',
    ];
    yield 'domainSuggestions' => [
      fn (Domain $d) => $d->domainSuggestions('cloud', 'com', true),
      'GET', '/api/domains/v5/suggest-names.json',
    ];
    yield 'transfer' => [
      fn (Domain $d) => $d->transfer('example.com', ['years' => 1]),
      'POST', '/api/domains/transfer.json',
    ];
    yield 'submitAuthCode' => [
      fn (Domain $d) => $d->submitAuthCode(123, 'auth'),
      'POST', '/api/domains/transfer/submit-auth-code.json',
    ];
    yield 'validateTransfer' => [
      fn (Domain $d) => $d->validateTransfer('example.com'),
      'GET', '/api/domains/validate-transfer.json',
    ];
    yield 'renew' => [
      fn (Domain $d) => $d->renew(123, ['years' => 1]),
      'POST', '/api/domains/renew.json',
    ];
    yield 'searchDomain' => [
      fn (Domain $d) => $d->searchDomain(['status' => 'active'], 2, 25),
      'GET', '/api/domains/search.json',
    ];
    yield 'getDefaultNameServer' => [
      fn (Domain $d) => $d->getDefaultNameServer('13647145'),
      'GET', '/api/domains/customer-default-ns.json',
    ];
    yield 'getOrderId' => [
      fn (Domain $d) => $d->getOrderId('example.com'),
      'GET', '/api/domains/orderid.json',
    ];
    yield 'getDomainDetailsByOrderId' => [
      fn (Domain $d) => $d->getDomainDetailsByOrderId(123, 'All'),
      'GET', '/api/domains/details.json',
    ];
    yield 'getDomainDetailsByDomain' => [
      fn (Domain $d) => $d->getDomainDetailsByDomain('example.com', 'All'),
      'GET', '/api/domains/details-by-name.json',
    ];
    yield 'setNameServer' => [
      fn (Domain $d) => $d->setNameServer(123, ['ns1.example.com', 'ns2.example.com']),
      'POST', '/api/domains/modify-ns.json',
    ];
    yield 'setChildNameServer' => [
      fn (Domain $d) => $d->setChildNameServer(123, 'ns1.example.com', ['1.2.3.4']),
      'POST', '/api/domains/add-cns.json',
    ];
    yield 'modifyChildNameServerHost' => [
      fn (Domain $d) => $d->modifyChildNameServerHost(123, 'old.example.com', 'new.example.com'),
      'POST', '/api/domains/modify-cns-name.json',
    ];
    yield 'modifyChildNameServerIp' => [
      fn (Domain $d) => $d->modifyChildNameServerIp(123, 'ns1.example.com', '1.2.3.4', '5.6.7.8'),
      'POST', '/api/domains/modify-cns-ip.json',
    ];
    yield 'deleteChildNameServer' => [
      fn (Domain $d) => $d->deleteChildNameServer(123, 'ns1.example.com', '1.2.3.4'),
      'POST', '/api/domains/delete-cns-ip.json',
    ];
    yield 'modifyDomainContacts' => [
      fn (Domain $d) => $d->modifyDomainContacts(123, ['reg-contact-id' => 47738316]),
      'POST', '/api/domains/modify-contact.json',
    ];
    yield 'addPrivacyProtection' => [
      fn (Domain $d) => $d->addPrivacyProtection(123, 'NoInvoice'),
      'POST', '/api/domains/purchase-privacy.json',
    ];
    yield 'modifyPrivacyProtection' => [
      fn (Domain $d) => $d->modifyPrivacyProtection(123, true, 'customer request'),
      'POST', '/api/domains/modify-privacy-protection.json',
    ];
    yield 'modifyAuthCode' => [
      fn (Domain $d) => $d->modifyAuthCode(123, 'newauth'),
      'POST', '/api/domains/modify-auth-code.json',
    ];
    yield 'modifyTheftProtection enable' => [
      fn (Domain $d) => $d->modifyTheftProtection(123, true),
      'POST', '/api/domains/enable-theft-protection.json',
    ];
    yield 'modifyTheftProtection disable' => [
      fn (Domain $d) => $d->modifyTheftProtection(123, false),
      'POST', '/api/domains/disable-theft-protection.json',
    ];
    yield 'suspendDomain' => [
      fn (Domain $d) => $d->suspendDomain(123, 'abuse'),
      'POST', '/api/orders/suspend.json',
    ];
    yield 'unsuspendDomain' => [
      fn (Domain $d) => $d->unsuspendDomain(123),
      'POST', '/api/orders/unsuspend.json',
    ];
    yield 'deleteDomain' => [
      fn (Domain $d) => $d->deleteDomain(123),
      'POST', '/api/domains/delete.json',
    ];
    yield 'restoreDomain' => [
      fn (Domain $d) => $d->restoreDomain(123, 'NoInvoice'),
      'POST', '/api/domains/restore.json',
    ];
  }
}
