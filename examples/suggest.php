<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

// Per https://manage.resellerclub.com/kb/answer/1085 , 2nd and 3rd level TLDs
// such as us and cc are supported.
$apiOut = $client->domains()->domainSuggestions('resellerclub', 'us');

var_dump($apiOut);
