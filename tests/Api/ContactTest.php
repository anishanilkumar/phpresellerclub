<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Resellerclub\Api\Contact;
use Resellerclub\Tests\Support\MockTransport;
use Resellerclub\Validation\Validator;

#[CoversClass(Contact::class)]
final class ContactTest extends TestCase
{
  public function testCreateContactValidatesAndPosts(): void
  {
    $transport = new MockTransport();
    $transport->willReturn('42');
    $contact = new Contact($transport->apiClient, new Validator());

    $result = $contact->createContact([
      'name' => 'Sherlock Holmes',
      'company' => 'N/A',
      'email' => 'sherlock.holmes@example.com',
      'address-line-1' => '221B Baker St.',
      'city' => 'London',
      'country' => 'IN',
      'zipcode' => '635426',
      'phone-cc' => '91',
      'phone' => '9876543210',
      'customer-id' => '13560800',
      'type' => 'Contact',
    ]);

    $request = $transport->lastRequest();
    self::assertSame('POST', $request->getMethod());
    self::assertSame('https://test.httpapi.com/api/contacts/add.json', (string) $request->getUri());
    self::assertStringContainsString('email=sherlock.holmes%40example.com', (string) $request->getBody());
    self::assertSame(42, $result);
  }

  /**
   * @param callable(Contact): mixed $call
   */
  #[DataProvider('callProvider')]
  public function testIssuesExpectedRequest(callable $call, string $method, string $pathContains): void
  {
    $transport = new MockTransport();
    $transport->willReturn();
    $contact = new Contact($transport->apiClient, new Validator());

    $call($contact);

    $request = $transport->lastRequest();
    self::assertSame($method, $request->getMethod());
    self::assertStringContainsString($pathContains, (string) $request->getUri());
  }

  /**
   * @return iterable<string, array{callable(Contact): mixed, string, string}>
   */
  public static function callProvider(): iterable
  {
    yield 'deleteContact' => [
      fn (Contact $c) => $c->deleteContact('47738316'),
      'POST', '/api/contacts/delete.json',
    ];
    yield 'editContact' => [
      fn (Contact $c) => $c->editContact('47738316', ['name' => 'New Name']),
      'POST', '/api/contacts/edit.json',
    ];
    yield 'getContact' => [
      fn (Contact $c) => $c->getContact('47738316'),
      'GET', '/api/contacts/details.json',
    ];
    yield 'searchContact' => [
      fn (Contact $c) => $c->searchContact('13560800', ['type' => 'Contact'], 20, 1),
      'GET', '/api/contacts/search.json',
    ];
  }
}
