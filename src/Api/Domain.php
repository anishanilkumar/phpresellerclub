<?php

declare(strict_types=1);

namespace Resellerclub\Api;

use Resellerclub\Http\HttpMethod;

/**
 * Domain-related API calls.
 *
 * @see https://manage.resellerclub.com/kb/node/764
 */
final class Domain extends AbstractApi
{
  /**
   * Check the availability of the specified domain name(s).
   *
   * @see https://manage.resellerclub.com/kb/answer/764
   *
   * @param string|list<string> $domainName Domain name(s) without TLDs.
   * @param string|list<string> $tlds       TLD(s) to check.
   */
  public function checkAvailability(
    string|array $domainName,
    string|array $tlds,
    bool $suggestAlternatives = false,
  ): mixed {
    $params = [
      'domain-name' => $domainName,
      'tlds' => $tlds,
      'suggest-alternative' => $suggestAlternatives,
    ];
    $this->validator->validate($params);

    return $this->call(HttpMethod::Get, 'domains', 'available', $params);
  }

  /**
   * Check the availability of Internationalized Domain Name(s) (IDN).
   *
   * @see https://manage.resellerclub.com/kb/answer/1427
   *
   * @param string|list<string> $domainName Unicode domain name(s).
   * @param string|list<string> $tld        TLD(s) to check.
   */
  public function checkAvailabilityIdn(
    string|array $domainName,
    string|array $tld,
    string $idnLanguageCode,
  ): mixed {
    $punycode = [];
    foreach ((array) $domainName as $domain) {
      $punycode[] = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    }
    $params = [
      'domain-name' => $punycode,
      'tlds' => $tld,
      'idn-languagecode' => $idnLanguageCode,
    ];
    $this->validator->validate($params);

    return $this->call(HttpMethod::Get, 'domains', 'idn-available', $params);
  }

  /**
   * Check availability of a premium domain name.
   *
   * @see https://manage.resellerclub.com/kb/answer/1948
   *
   * @param string|list<string> $tlds
   * @param array<string, mixed> $options
   */
  public function checkAvailabilityPremium(string $keyWord, string|array $tlds, array $options): mixed
  {
    $options['key-word'] = $keyWord;
    $options['tlds'] = $tlds;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'domains', 'available', $options, 'premium');
  }

  /**
   * Return domain name suggestions for a keyword.
   *
   * @see https://manage.resellerclub.com/kb/answer/1085
   */
  public function domainSuggestions(string $keyWord, ?string $tld = null, bool $exactMatch = false): mixed
  {
    $options = [
      'keyword' => $keyWord,
      'tld-only' => $tld,
      'exact-match' => $exactMatch,
    ];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'domains', 'suggest-names', $options, 'v5');
  }

  /**
   * Register a domain name.
   *
   * @see https://manage.resellerclub.com/kb/answer/752
   *
   * @param array<string, mixed> $options
   */
  public function register(string $domainName, array $options): mixed
  {
    $options['domain-name'] = $domainName;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'register', $options);
  }

  /**
   * Transfer a domain name.
   *
   * @see https://manage.resellerclub.com/kb/answer/758
   *
   * @param array<string, mixed> $options
   */
  public function transfer(string $domain, array $options): mixed
  {
    $options['domain'] = $domain;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'transfer', $options);
  }

  /**
   * Submit an auth code for a domain transfer.
   *
   * @see https://manage.resellerclub.com/kb/answer/2447
   */
  public function submitAuthCode(int $orderId, string $authCode): mixed
  {
    $options = ['order-id' => $orderId, 'auth-code' => $authCode];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'submit-auth-code', $options, 'transfer');
  }

  /**
   * Validate a transfer request.
   *
   * @see https://manage.resellerclub.com/kb/answer/1150
   */
  public function validateTransfer(string $domain): mixed
  {
    $options = ['domain-name' => $domain];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'domains', 'validate-transfer', $options);
  }

  /**
   * Renew a domain.
   *
   * @see https://manage.resellerclub.com/kb/answer/746
   *
   * @param array<string, mixed> $options
   */
  public function renew(int $orderId, array $options): mixed
  {
    $options['order-id'] = $orderId;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'renew', $options);
  }

  /**
   * Search domains.
   *
   * @see https://manage.resellerclub.com/kb/answer/771
   *
   * @param array<string, mixed> $options
   */
  public function searchDomain(array $options, int $page = 1, int $count = 10): mixed
  {
    $options['no-of-records'] = $count;
    $options['page-no'] = $page;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'domains', 'search', $options);
  }

  /**
   * Get the default nameservers for a customer.
   *
   * @see https://manage.resellerclub.com/kb/answer/788
   */
  public function getDefaultNameServer(int|string $customerId): mixed
  {
    $options = ['customer-id' => $customerId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'domains', 'customer-default-ns', $options);
  }

  /**
   * Get the order ID for a domain name.
   *
   * @see https://manage.resellerclub.com/kb/answer/763
   */
  public function getOrderId(string $domain): mixed
  {
    $options = ['domain' => $domain];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Get, 'domains', 'orderid', $options);
  }

  /**
   * Get domain details by order ID.
   *
   * @see https://manage.resellerclub.com/kb/answer/770
   */
  public function getDomainDetailsByOrderId(int $orderId, string $options): mixed
  {
    $params = ['order-id' => $orderId, 'options' => $options];
    $this->validator->validate($params);

    return $this->call(HttpMethod::Get, 'domains', 'details', $params);
  }

  /**
   * Get domain details by domain name.
   *
   * @see https://manage.resellerclub.com/kb/answer/1755
   */
  public function getDomainDetailsByDomain(string $domain, string $options): mixed
  {
    $params = ['domain-name' => $domain, 'options' => $options];
    $this->validator->validate($params);

    return $this->call(HttpMethod::Get, 'domains', 'details-by-name', $params);
  }

  /**
   * Set the nameservers for an order.
   *
   * @see https://manage.resellerclub.com/kb/answer/776
   *
   * @param list<string> $ns
   */
  public function setNameServer(int $orderId, array $ns): mixed
  {
    $options = ['order-id' => $orderId, 'ns' => $ns];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'modify-ns', $options);
  }

  /**
   * Add a child nameserver for a domain.
   *
   * @see https://manage.resellerclub.com/kb/answer/780
   *
   * @param list<string> $ips
   */
  public function setChildNameServer(int $orderId, string $cns, array $ips): mixed
  {
    $options = ['order-id' => $orderId, 'cns' => $cns, 'ip' => $ips];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'add-cns', $options);
  }

  /**
   * Modify a child nameserver hostname.
   *
   * @see https://manage.resellerclub.com/kb/answer/781
   */
  public function modifyChildNameServerHost(int $orderId, string $oldCns, string $newCns): mixed
  {
    $options = ['order-id' => $orderId, 'old-cns' => $oldCns, 'new-cns' => $newCns];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'modify-cns-name', $options);
  }

  /**
   * Modify a child nameserver's IP address.
   *
   * @see https://manage.resellerclub.com/kb/answer/782
   */
  public function modifyChildNameServerIp(int $orderId, string $cns, string $oldIp, string $newIp): mixed
  {
    $options = ['order-id' => $orderId, 'cns' => $cns, 'old-ip' => $oldIp, 'new-ip' => $newIp];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'modify-cns-ip', $options);
  }

  /**
   * Delete a child nameserver.
   *
   * @see https://manage.resellerclub.com/kb/answer/934
   */
  public function deleteChildNameServer(int $orderId, string $cns, string $ip): mixed
  {
    $options = ['order-id' => $orderId, 'cns' => $cns, 'ip' => $ip];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'delete-cns-ip', $options);
  }

  /**
   * Modify the contacts of a domain name.
   *
   * @see https://manage.resellerclub.com/kb/answer/777
   *
   * @param array<string, mixed> $contactIds
   */
  public function modifyDomainContacts(int $orderId, array $contactIds): mixed
  {
    $options = $contactIds;
    $options['order-id'] = $orderId;
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'modify-contact', $options);
  }

  /**
   * Purchase privacy protection for a domain.
   *
   * @see https://manage.resellerclub.com/kb/answer/2085
   */
  public function addPrivacyProtection(int $orderId, string $invoiceOption): mixed
  {
    $options = ['order-id' => $orderId, 'invoice-option' => $invoiceOption];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'purchase-privacy', $options);
  }

  /**
   * Enable or disable privacy protection for an order.
   *
   * @see https://manage.resellerclub.com/kb/answer/778
   */
  public function modifyPrivacyProtection(int $orderId, bool $protectPrivacy, string $reason): mixed
  {
    $options = ['order-id' => $orderId, 'protect-privacy' => $protectPrivacy, 'reason' => $reason];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'modify-privacy-protection', $options);
  }

  /**
   * Modify the domain transfer auth code.
   *
   * @see https://manage.resellerclub.com/kb/answer/779
   */
  public function modifyAuthCode(int $orderId, string $authCode): mixed
  {
    $options = ['order-id' => $orderId, 'auth-code' => $authCode];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'modify-auth-code', $options);
  }

  /**
   * Enable or disable theft protection.
   *
   * @see https://manage.resellerclub.com/kb/answer/902
   * @see https://manage.resellerclub.com/kb/answer/903
   */
  public function modifyTheftProtection(int $orderId, bool $status): mixed
  {
    $options = ['order-id' => $orderId];
    $this->validator->validate($options);
    $apiName = $status ? 'enable-theft-protection' : 'disable-theft-protection';

    return $this->call(HttpMethod::Post, 'domains', $apiName, $options);
  }

  /**
   * Suspend a domain order.
   *
   * @see https://manage.resellerclub.com/kb/answer/1451
   */
  public function suspendDomain(int $orderId, string $reason): mixed
  {
    $options = ['order-id' => $orderId, 'reason' => $reason];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'orders', 'suspend', $options);
  }

  /**
   * Unsuspend a domain order.
   *
   * @see https://manage.resellerclub.com/kb/answer/1452
   */
  public function unsuspendDomain(int $orderId): mixed
  {
    $options = ['order-id' => $orderId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'orders', 'unsuspend', $options);
  }

  /**
   * Delete a domain.
   *
   * @see https://manage.resellerclub.com/kb/answer/745
   */
  public function deleteDomain(int $orderId): mixed
  {
    $options = ['order-id' => $orderId];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'delete', $options);
  }

  /**
   * Restore a deleted domain.
   *
   * @see https://manage.resellerclub.com/kb/answer/760
   */
  public function restoreDomain(int $orderId, string $invoiceOption): mixed
  {
    $options = ['order-id' => $orderId, 'invoice-option' => $invoiceOption];
    $this->validator->validate($options);

    return $this->call(HttpMethod::Post, 'domains', 'restore', $options);
  }
}
