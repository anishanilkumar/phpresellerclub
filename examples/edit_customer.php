<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$customerId = '13620823';

$apiOut = $client->customers()->editCustomer($customerId, [
    'username' => 'random@example.com',
    'passwd' => 'Rand124',
    'name' => 'John Doe',
    'company' => 'N/A',
    'address-line-1' => 'Test Address Line',
    'city' => 'Thiruvananthapuram',
    'state' => 'Kerala',
    'country' => 'IN',
    'zipcode' => '695009',
    'phone-cc' => '91',
    'phone' => '9876543210',
    'lang-pref' => 'en',
]);

var_dump($apiOut);
