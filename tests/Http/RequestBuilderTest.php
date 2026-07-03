<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Resellerclub\Config\Environment;
use Resellerclub\Exception\InvalidUrlArrayException;
use Resellerclub\Http\RequestBuilder;

#[CoversClass(RequestBuilder::class)]
final class RequestBuilderTest extends TestCase
{
  public function testBuildsPathWithoutSection2(): void
  {
    $builder = new RequestBuilder(Environment::Test);

    self::assertSame(
      'https://test.httpapi.com/api/domains/available.json',
      $builder->buildPath('domains', 'available'),
    );
  }

  public function testBuildsPathWithSection2(): void
  {
    $builder = new RequestBuilder(Environment::Test);

    self::assertSame(
      'https://test.httpapi.com/api/domains/premium/available.json',
      $builder->buildPath('domains', 'available', 'premium'),
    );
  }

  public function testBuildsPathAgainstProductionHost(): void
  {
    $builder = new RequestBuilder(Environment::Production);

    self::assertSame(
      'https://httpapi.com/api/domains/available.json',
      $builder->buildPath('domains', 'available'),
    );
  }

  /**
   * @param array<string, mixed> $parameters
   */
  #[DataProvider('queryProvider')]
  public function testBuildsQuery(array $parameters, string $expected): void
  {
    self::assertSame($expected, (new RequestBuilder())->buildQuery($parameters));
  }

  /**
   * @return iterable<string, array{array<string, mixed>, string}>
   */
  public static function queryProvider(): iterable
  {
    yield 'empty array' => [[], ''];

    yield 'simple parameters' => [
      ['auth-userid' => 'xxxx', 'api-key' => 'yyyy', 'domain-name' => 'domain1', 'tlds' => 'com'],
      'auth-userid=xxxx&api-key=yyyy&domain-name=domain1&tlds=com',
    ];

    yield 'list parameters expand into repeated keys' => [
      ['domain-name' => ['domain1', 'domain2'], 'tlds' => ['com', 'net']],
      'domain-name=domain1&domain-name=domain2&tlds=com&tlds=net',
    ];

    yield 'booleans are stringified' => [
      ['suggest-alternative' => false, 'exact-match' => true],
      'suggest-alternative=false&exact-match=true',
    ];

    yield 'integers are stringified' => [
      ['no-of-records' => 10, 'page-no' => 1],
      'no-of-records=10&page-no=1',
    ];

    yield 'values are url-encoded' => [
      ['keyword' => 'a b&c'],
      'keyword=a+b%26c',
    ];
  }

  public function testThrowsOnNullValue(): void
  {
    $this->expectException(InvalidUrlArrayException::class);

    (new RequestBuilder())->buildQuery(['broken' => null]);
  }

  public function testThrowsOnObjectValue(): void
  {
    $this->expectException(InvalidUrlArrayException::class);

    (new RequestBuilder())->buildQuery(['broken' => new \stdClass()]);
  }
}
