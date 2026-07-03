<?php

/**
 * Shared bootstrap for the example scripts.
 *
 * Builds a ResellerClub client from environment variables so the examples never
 * hardcode credentials:
 *
 *   RESELLER_ID=0000000 RESELLER_API_KEY=xxxx php examples/search.php
 *
 * By default it talks to the Test (OT&E) environment. Set RESELLER_ENV=production
 * to hit the live API.
 */

declare(strict_types=1);

use Resellerclub\Config\Credentials;
use Resellerclub\Config\Environment;
use Resellerclub\ResellerClub;

require __DIR__ . '/../vendor/autoload.php';

$isProduction = getenv('RESELLER_ENV') === 'production';
$environment = $isProduction ? Environment::Production : Environment::Test;

return new ResellerClub(
  new Credentials(
    (string) (getenv('RESELLER_ID') ?: '0000000'),
    (string) (getenv('RESELLER_API_KEY') ?: 'your-api-key'),
    $environment,
  )
);
