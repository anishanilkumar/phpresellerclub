<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$customerId = '13647145';
$contactId = '47738316';

$apiOut = $client->domains()->register('example.com', [
  'years' => 1,
  'ns' => ['ns1.onlyfordemo.net', 'ns2.onlyfordemo.net'],
  'customer-id' => $customerId,
  'reg-contact-id' => $contactId,
  'admin-contact-id' => $contactId,
  'tech-contact-id' => $contactId,
  'billing-contact-id' => $contactId,
  'invoice-option' => 'NoInvoice',
]);

var_dump($apiOut);
