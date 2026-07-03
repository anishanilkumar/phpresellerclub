<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$orderId = 12345678;

$apiOut = $client->domains()->setNameServer($orderId, ['ns1.example.com', 'ns2.example.com']);

var_dump($apiOut);
