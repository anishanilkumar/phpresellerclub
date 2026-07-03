<?php

declare(strict_types=1);

namespace Resellerclub\Api;

use Resellerclub\Http\HttpMethod;

/**
 * Billing, transaction and reference-data API calls.
 *
 * @see https://manage.resellerclub.com/kb/node/864
 */
final class Billing extends AbstractApi
{
  /**
   * Get a customer's pricing.
   *
   * @see https://manage.resellerclub.com/kb/answer/864
   */
  public function getCustomerPricing(int|string $customerId): mixed
  {
    $options = ['customer-id' => $customerId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'products', 'customer-price', $options);
  }

  /**
   * Get a reseller's pricing.
   *
   * @see https://manage.resellerclub.com/kb/answer/865
   */
  public function getResellerPricing(int|string $resellerId): mixed
  {
    $options = ['reseller-id' => $resellerId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'products', 'reseller-price', $options);
  }

  /**
   * Get a reseller's cost pricing.
   *
   * @see https://manage.resellerclub.com/kb/answer/1029
   */
  public function getResellerCostPricing(int|string $resellerId): mixed
  {
    $options = ['reseller-id' => $resellerId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'products', 'reseller-cost-price', $options);
  }

  /**
   * Get a customer's transaction details.
   *
   * @see https://manage.resellerclub.com/kb/answer/868
   *
   * @param int|string|list<int|string> $transactionIds
   */
  public function getCustomerTransactionDetails(int|string|array $transactionIds): mixed
  {
    $options = ['transaction-ids' => $transactionIds];

    return $this->call(HttpMethod::Get, 'products', 'customer-transactions', $options);
  }

  /**
   * Get a reseller's transaction details.
   *
   * @see https://manage.resellerclub.com/kb/answer/1155
   *
   * @param int|string|list<int|string> $transactionIds
   */
  public function getResellerTransactionDetails(int|string|array $transactionIds): mixed
  {
    $options = ['transaction-ids' => $transactionIds];

    return $this->call(HttpMethod::Get, 'products', 'reseller-transactions', $options);
  }

  /**
   * Pay pending transactions from the account balance.
   *
   * @see https://manage.resellerclub.com/kb/answer/871
   *
   * @param list<int|string> $invoiceIds
   * @param list<int|string> $debitIds
   */
  public function payTransactions(array $invoiceIds = [], array $debitIds = []): mixed
  {
    $options = ['invoice-ids' => $invoiceIds, 'debit-ids' => $debitIds];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'customer-pay', $options);
  }

  /**
   * Cancel invoice(s) and/or debit note(s).
   *
   * @see https://manage.resellerclub.com/kb/answer/2415
   *
   * @param list<int|string> $invoiceIds
   * @param list<int|string> $debitIds
   */
  public function cancelInvoiceDebitNote(array $invoiceIds = [], array $debitIds = []): mixed
  {
    $options = ['invoice-ids' => $invoiceIds, 'debit-ids' => $debitIds];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'cancel', $options, 'customer-transactions');
  }

  /**
   * Get a customer's account balance.
   *
   * @see https://manage.resellerclub.com/kb/answer/872
   */
  public function getCustomerBalance(int|string $customerId): mixed
  {
    $options = ['customer-id' => $customerId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'billing', 'customer-balance', $options);
  }

  /**
   * Execute an order without collecting payment.
   *
   * @see https://manage.resellerclub.com/kb/answer/873
   *
   * @param list<int|string> $invoiceIds
   */
  public function executeOrderWithoutPayment(array $invoiceIds, bool $cancelInvoice = false): mixed
  {
    $options = ['invoice-ids' => $invoiceIds, 'cancel-invoice' => $cancelInvoice];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'execute-order-without-payment', $options);
  }

  /**
   * Search a customer's transactions.
   *
   * @see https://manage.resellerclub.com/kb/answer/964
   *
   * @param array<string, mixed> $options
   */
  public function searchCustomerTransaction(array $options, int $page = 1, int $count = 10): mixed
  {
    $options['no-of-records'] = $count;
    $options['page-no'] = $page;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'billing', 'search', $options, 'customer-transactions');
  }

  /**
   * Search a reseller's transactions.
   *
   * @see https://manage.resellerclub.com/kb/answer/1153
   *
   * @param array<string, mixed> $options
   */
  public function searchResellerTransaction(array $options, int $page = 1, int $count = 10): mixed
  {
    $options['no-of-records'] = $count;
    $options['page-no'] = $page;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'billing', 'search', $options, 'reseller-transactions');
  }

  /**
   * Get a reseller's account balance.
   *
   * @see https://manage.resellerclub.com/kb/answer/1110
   */
  public function getResellerBalance(int|string $resellerId): mixed
  {
    $options = ['reseller-id' => $resellerId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'billing', 'reseller-balance', $options);
  }

  /**
   * Add a discount to an invoice.
   *
   * @see https://manage.resellerclub.com/kb/answer/2414
   */
  public function discountInvoice(
    int|string $invoiceId,
    float $discount,
    string $transactionKey,
    string $role,
  ): mixed {
    $options = [
      'invoice-id' => $invoiceId,
      'discount-without-tax' => $discount,
      'transaction-key' => $transactionKey,
      'role' => $role,
    ];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'customer-processdiscount', $options);
  }

  /**
   * Add funds to a customer's account.
   *
   * @see https://manage.resellerclub.com/kb/answer/1152
   *
   * @param array<string, mixed> $options
   */
  public function addFundsCustomer(int|string $customerId, array $options): mixed
  {
    $options['customer-id'] = $customerId;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'add-customer-fund', $options);
  }

  /**
   * Add funds to a reseller's account.
   *
   * @see https://manage.resellerclub.com/kb/answer/1151
   *
   * @param array<string, mixed> $options
   */
  public function addFundsReseller(int|string $resellerId, array $options): mixed
  {
    $options['reseller-id'] = $resellerId;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'add-reseller-fund', $options);
  }

  /**
   * Add a debit note to a customer's account.
   *
   * @see https://manage.resellerclub.com/kb/answer/1166
   *
   * @param array<string, mixed> $options
   */
  public function addDebitNoteCustomer(int|string $customerId, array $options): mixed
  {
    $options['customer-id'] = $customerId;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'add-customer-debit-note', $options);
  }

  /**
   * Add a debit note to a reseller's account.
   *
   * @see https://manage.resellerclub.com/kb/answer/1167
   *
   * @param array<string, mixed> $options
   */
  public function addDebitNoteReseller(int|string $resellerId, array $options): mixed
  {
    $options['reseller-id'] = $resellerId;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'billing', 'add-reseller-debit-note', $options);
  }

  /**
   * Suspend an order.
   *
   * @see https://manage.resellerclub.com/kb/answer/1077
   */
  public function suspendOrder(int $orderId, string $reason): mixed
  {
    $options = ['order-id' => $orderId, 'reason' => $reason];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'orders', 'suspend', $options);
  }

  /**
   * Unsuspend an order.
   *
   * @see https://manage.resellerclub.com/kb/answer/1078
   */
  public function unsuspendOrder(int $orderId): mixed
  {
    $options = ['order-id' => $orderId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'orders', 'unsuspend', $options);
  }

  /**
   * Search current actions.
   *
   * @see https://manage.resellerclub.com/kb/answer/908
   *
   * @param array<string, mixed> $options
   */
  public function getCurrentActions(array $options, int $page = 1, int $count = 10): mixed
  {
    $options['no-of-records'] = $count;
    $options['page-no'] = $page;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'actions', 'search-current', $options);
  }

  /**
   * Search archived actions.
   *
   * @see https://manage.resellerclub.com/kb/answer/909
   *
   * @param array<string, mixed> $options
   */
  public function getArchiveActions(array $options, int $page = 1, int $count = 10): mixed
  {
    $options['no-of-records'] = $count;
    $options['page-no'] = $page;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'actions', 'search-archived', $options);
  }

  /**
   * Get default and customised legal agreements.
   *
   * @see https://manage.resellerclub.com/kb/answer/835
   */
  public function getLegalAgreement(string $type): mixed
  {
    $options = ['type' => $type];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'commons', 'legal-agreements', $options);
  }

  /**
   * Get allowed payment gateways for a customer.
   *
   * @param string|null $paymentType One of "AddFund" or "Payment".
   */
  public function getAllowedPaymentGatewayCustomer(int|string $customerId, ?string $paymentType = null): mixed
  {
    $options = ['customer-id' => $customerId];
    if ($paymentType !== null && $paymentType !== '') {
      $options['payment-type'] = $paymentType;
    }
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'pg', 'allowedlist-for-customer', $options);
  }

  /**
   * Get allowed payment gateways for the reseller.
   */
  public function getAllowedPaymentGatewayReseller(): mixed
  {
    return $this->call(HttpMethod::Get, 'pg', 'list-for-reseller');
  }

  /**
   * Get the list of approved currencies.
   *
   * @see https://manage.resellerclub.com/kb/answer/1745
   */
  public function getCurrencyDetails(): mixed
  {
    return $this->call(HttpMethod::Get, 'currency', 'details');
  }

  /**
   * Get the list of countries.
   *
   * @see https://manage.resellerclub.com/kb/answer/1746
   */
  public function getCountryList(): mixed
  {
    return $this->call(HttpMethod::Get, 'country', 'list');
  }

  /**
   * Get the list of states for a country.
   *
   * @see https://manage.resellerclub.com/kb/answer/1747
   *
   * @param string $countryCode Two-letter country code.
   */
  public function getStateList(string $countryCode): mixed
  {
    $options = ['country-code' => $countryCode];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'country', 'state-list', $options);
  }
}
