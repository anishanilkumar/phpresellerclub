<?php

declare(strict_types=1);

/** @var \Resellerclub\ResellerClub $client */
$client = require __DIR__ . '/bootstrap.php';

$orderId = 12345678;
$contactId = '47737452';

$apiOut = $client->domains()->modifyDomainContacts($orderId, [
    'reg-contact-id' => $contactId,
    'admin-contact-id' => $contactId,
    'tech-contact-id' => $contactId,
    'billing-contact-id' => $contactId,
]);

var_dump($apiOut);
