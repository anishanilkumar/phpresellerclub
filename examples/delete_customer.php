<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$customerId = '13620823';

$apiOut = $client->customers()->deleteCustomer($customerId);

var_dump($apiOut);
