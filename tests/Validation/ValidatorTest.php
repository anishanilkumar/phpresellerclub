<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Resellerclub\Exception\InvalidArrayException;
use Resellerclub\Exception\InvalidItemException;
use Resellerclub\Exception\InvalidParameterException;
use Resellerclub\Exception\MissingParameterException;
use Resellerclub\Validation\Validator;

#[CoversClass(Validator::class)]
final class ValidatorTest extends TestCase
{
  private Validator $validator;

  protected function setUp(): void
  {
    $this->validator = new Validator();
  }

  #[DataProvider('emailProvider')]
  public function testIsEmail(string $email, bool $expected): void
  {
    self::assertSame($expected, Validator::isEmail($email));
  }

  /**
   * @return iterable<string, array{string, bool}>
   */
  public static function emailProvider(): iterable
  {
    yield 'valid' => ['sherlock.holmes@example.com', true];
    yield 'numbers only' => ['123456', false];
    yield 'missing tld' => ['anish@gmail', false];
    yield 'trailing dot' => ['a@g.', false];
  }

  public function testIsIp(): void
  {
    self::assertTrue(Validator::isIp('127.0.0.1'));
    self::assertTrue(Validator::isIp('::1'));
    self::assertFalse(Validator::isIp('not-an-ip'));
  }

  public function testIsCustomerId(): void
  {
    self::assertTrue(Validator::isCustomerId('13560800'));
    self::assertTrue(Validator::isCustomerId(13560800));
    self::assertFalse(Validator::isCustomerId('123'));
    self::assertFalse(Validator::isCustomerId('abcdefgh'));
  }

  public function testIsContactId(): void
  {
    self::assertTrue(Validator::isContactId('47738316'));
    self::assertFalse(Validator::isContactId('4773'));
  }

  public function testValidatePassesForGenericArray(): void
  {
    $this->expectNotToPerformAssertions();

    $this->validator->validate(['domain-name' => 'example', 'tlds' => ['com', 'net']]);
  }

  public function testValidateRunsItemValidatorsOnRecognisedKeys(): void
  {
    $this->expectException(InvalidItemException::class);

    $this->validator->validate(['email' => 'not an email']);
  }

  public function testValidateRejectsNonScalarNestedValues(): void
  {
    $this->expectException(InvalidArrayException::class);

    $this->validator->validate(['nested' => [['too', 'deep']]]);
  }

  public function testValidateRejectsNonScalarTopLevelValue(): void
  {
    $this->expectException(InvalidArrayException::class);

    $this->validator->validate(['obj' => new \stdClass()]);
  }

  public function testValidateAcceptsScalarIpAndContactId(): void
  {
    $this->expectNotToPerformAssertions();

    $this->validator->validate(['ip' => '1.2.3.4', 'contact-id' => '47738316']);
  }

  public function testValidateRejectsInvalidIpInAList(): void
  {
    $this->expectException(InvalidItemException::class);

    $this->validator->validate(['ip' => ['1.2.3.4', 'not-an-ip']]);
  }

  public function testValidateContactAcceptsValidContact(): void
  {
    $this->expectNotToPerformAssertions();

    $this->validator->validateContact(self::validContact());
  }

  public function testValidateContactRejectsInvalidEmail(): void
  {
    $contact = self::validContact();
    $contact['email'] = 'james watson example.com';

    $this->expectException(InvalidItemException::class);

    $this->validator->validateContact($contact);
  }

  public function testValidateContactRejectsMissingMandatoryField(): void
  {
    $contact = self::validContact();
    unset($contact['city']);

    $this->expectException(MissingParameterException::class);

    $this->validator->validateContact($contact);
  }

  public function testValidateContactRejectsUnexpectedParameter(): void
  {
    $contact = self::validContact();
    $contact['not-allowed'] = 'value';

    $this->expectException(InvalidParameterException::class);

    $this->validator->validateContact($contact);
  }

  public function testValidateCustomerAcceptsValidCustomer(): void
  {
    $this->expectNotToPerformAssertions();

    $this->validator->validateCustomer([
      'username' => 'regina.phelange@example.com',
      'passwd' => 'Rand@123om',
      'name' => 'Regina Phelange',
      'company' => 'N/A',
      'address-line-1' => 'Test Address Line',
      'city' => 'New York',
      'state' => 'NY',
      'country' => 'US',
      'zipcode' => '567889',
      'phone-cc' => '91',
      'phone' => '9876543210',
      'lang-pref' => 'en',
    ]);
  }

  /**
   * @return array<string, string>
   */
  private static function validContact(): array
  {
    return [
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
    ];
  }
}
