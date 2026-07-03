<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$apiOut = $client->domains()->checkAvailability('resellerclub', 'com', true);

var_dump($apiOut);
