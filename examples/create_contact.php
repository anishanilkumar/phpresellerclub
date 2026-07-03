<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$apiOut = $client->contacts()->createContact([
    'name' => 'Anish Sheela',
    'company' => 'N/A',
    'email' => 'anishsheela@outlook.com',
    'address-line-1' => '221B Baker St.',
    'city' => 'London',
    'country' => 'IN',
    'zipcode' => '635426',
    'phone-cc' => '91',
    'phone' => '9876543210',
    'customer-id' => '13647145',
    'type' => 'Contact',
]);

var_dump($apiOut);
