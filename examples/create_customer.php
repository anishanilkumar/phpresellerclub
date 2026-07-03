<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$apiOut = $client->customers()->createCustomer([
    'username' => 'newmail@example.com',
    'passwd' => 'r@ndomP@sswd',
    'name' => 'Jane Doe',
    'company' => 'N/A',
    'address-line-1' => 'Test Address Line',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'country' => 'IN',
    'zipcode' => '567889',
    'phone-cc' => '91',
    'phone' => '9876543210',
    'lang-pref' => 'en',
]);

var_dump($apiOut);
