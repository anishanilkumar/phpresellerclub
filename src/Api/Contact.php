<?php

declare(strict_types=1);

namespace Resellerclub\Api;

use Resellerclub\Http\HttpMethod;

/**
 * Contact-related API calls.
 *
 * @see https://manage.resellerclub.com/kb/node/790
 */
final class Contact extends AbstractApi
{
  /**
   * Create a contact.
   *
   * @see https://manage.resellerclub.com/kb/answer/790
   *
   * @param array<string, mixed> $contactDetails
   */
  public function createContact(array $contactDetails): mixed
  {
    $this->validator->validateContact($contactDetails);

    return $this->call(HttpMethod::Post, 'contacts', 'add', $contactDetails);
  }

  /**
   * Delete a contact.
   *
   * @see https://manage.resellerclub.com/kb/answer/796
   */
  public function deleteContact(int|string $contactId): mixed
  {
    $params = ['contact-id' => $contactId];
    $this->validator->validate($params);

    return $this->call(HttpMethod::Post, 'contacts', 'delete', $params);
  }

  /**
   * Modify a contact's details.
   *
   * @see https://manage.resellerclub.com/kb/answer/791
   *
   * @param array<string, mixed> $contactDetails
   */
  public function editContact(int|string $contactId, array $contactDetails): mixed
  {
    $contactDetails['contact-id'] = $contactId;
    $this->validator->validate($contactDetails);

    return $this->call(HttpMethod::Post, 'contacts', 'edit', $contactDetails);
  }

  /**
   * Get a contact's details by ID.
   *
   * @see https://manage.resellerclub.com/kb/answer/792
   */
  public function getContact(int|string $contactId): mixed
  {
    $params = ['contact-id' => $contactId];
    $this->validator->validate($params);

    return $this->call(HttpMethod::Get, 'contacts', 'details', $params);
  }

  /**
   * Search for contacts belonging to a customer.
   *
   * @see https://manage.resellerclub.com/kb/answer/793
   *
   * @param array<string, mixed> $criteria
   */
  public function searchContact(int|string $customerId, array $criteria = [], int $count = 10, int $page = 0): mixed
  {
    $criteria['customer-id'] = $customerId;
    $criteria['no-of-records'] = $count;
    $criteria['page-no'] = $page;
    $this->validator->validate($criteria);

    return $this->call(HttpMethod::Get, 'contacts', 'search', $criteria);
  }
}
