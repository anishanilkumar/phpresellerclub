<?php

declare(strict_types=1);

namespace Resellerclub\Validation;

use Resellerclub\Exception\InvalidArrayException;
use Resellerclub\Exception\InvalidItemException;
use Resellerclub\Exception\InvalidParameterException;
use Resellerclub\Exception\MissingParameterException;

/**
 * Validates parameter arrays before they are sent to the API.
 *
 * Ports the rules of the old `Validation` class into a standalone, injectable
 * component (no inheritance from the transport layer). Individual items whose
 * key is recognised (email, ip, customer-id, contact-id) are checked with a
 * dedicated validator; everything else is passed through.
 */
final class Validator
{
  /** Contact fields that must be present when creating a contact. */
  public const CONTACT_MANDATORY = [
    'name', 'company', 'email', 'address-line-1', 'city', 'country',
    'zipcode', 'phone-cc', 'phone', 'customer-id', 'type',
  ];

  /** Contact fields that may optionally be present. */
  public const CONTACT_OPTIONAL = [
    'contact-id', 'address-line-2', 'address-line-3', 'state',
    'fax-cc', 'fax', 'attr-name', 'attr-value',
  ];

  /** Customer fields that must be present when signing up a customer. */
  public const CUSTOMER_MANDATORY = [
    'username', 'passwd', 'name', 'company', 'address-line-1', 'city',
    'state', 'country', 'zipcode', 'phone-cc', 'phone', 'lang-pref',
  ];

  /** Customer fields that may optionally be present. */
  public const CUSTOMER_OPTIONAL = [
    'other-state', 'address-line-2', 'address-line-3', 'alt-phone-cc',
    'alt-phone', 'fax-cc', 'fax', 'mobile-cc', 'mobile', 'customer-id',
  ];

  /**
   * Validate a generic parameter array (no mandatory/optional restriction).
   *
   * @param array<string, mixed> $parameters
   */
  public function validate(array $parameters): void
  {
    $this->validateArray($parameters, [], []);
  }

  /**
   * Validate a contact-details array.
   *
   * @param array<string, mixed> $contact
   */
  public function validateContact(array $contact): void
  {
    $this->validateArray($contact, self::CONTACT_MANDATORY, self::CONTACT_OPTIONAL);
  }

  /**
   * Validate a customer-details array.
   *
   * @param array<string, mixed> $customer
   */
  public function validateCustomer(array $customer): void
  {
    $this->validateArray($customer, self::CUSTOMER_MANDATORY, self::CUSTOMER_OPTIONAL);
  }

  public static function isEmail(string $email): bool
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

  public static function isIp(string $ip): bool
  {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
  }

  public static function isCustomerId(int|string $customerId): bool
  {
    return is_numeric($customerId) && strlen((string) $customerId) === 8;
  }

  public static function isContactId(int|string $contactId): bool
  {
    return is_numeric($contactId) && strlen((string) $contactId) === 8;
  }

  /**
   * @param array<string, mixed> $input
   * @param list<string>         $mandatory
   * @param list<string>         $optional
   *
   * @throws InvalidArrayException
   * @throws InvalidParameterException
   * @throws MissingParameterException
   * @throws InvalidItemException
   */
  private function validateArray(array $input, array $mandatory, array $optional): void
  {
    $restricted = $optional !== [] || $mandatory !== [];

    foreach ($input as $key => $value) {
      if ($restricted && !in_array($key, $mandatory, true) && !in_array($key, $optional, true)) {
        throw new InvalidParameterException(sprintf('Unexpected parameter "%s".', $key));
      }
      $this->assertScalarOrScalarList($key, $value);
      if (!$this->validateItem($key, $value)) {
        throw new InvalidItemException(sprintf('Value for "%s" is invalid.', $key));
      }
    }

    foreach ($mandatory as $required) {
      if (!isset($input[$required])) {
        throw new MissingParameterException(sprintf('Mandatory parameter "%s" is missing.', $required));
      }
    }
  }

  /**
   * @throws InvalidArrayException
   */
  private function assertScalarOrScalarList(string $key, mixed $value): void
  {
    if (is_array($value)) {
      foreach ($value as $item) {
        if (!$this->isScalarOrNull($item)) {
          throw new InvalidArrayException(sprintf('Parameter "%s" contains a non-scalar item.', $key));
        }
      }

      return;
    }

    if (!$this->isScalarOrNull($value)) {
      throw new InvalidArrayException(sprintf('Parameter "%s" must be a scalar or an array of scalars.', $key));
    }
  }

  private function isScalarOrNull(mixed $value): bool
  {
    return $value === null || is_scalar($value);
  }

  /**
   * Run the dedicated validator for a recognised key. Unknown keys pass.
   * List values are validated element by element.
   */
  private function validateItem(string $key, mixed $value): bool
  {
    if (is_array($value)) {
      foreach ($value as $item) {
        if (!$this->validateScalarItem($key, $item)) {
          return false;
        }
      }

      return true;
    }

    return $this->validateScalarItem($key, $value);
  }

  private function validateScalarItem(string $key, mixed $value): bool
  {
    return match ($key) {
      'email', 'username' => is_string($value) && self::isEmail($value),
      'ip' => is_string($value) && self::isIp($value),
      'customer-id' => (is_string($value) || is_int($value)) && self::isCustomerId($value),
      'contact-id' => (is_string($value) || is_int($value)) && self::isContactId($value),
      default => true,
    };
  }
}
