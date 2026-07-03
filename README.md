# phpresellerclub

[![CI](https://github.com/anishanilkumar/phpresellerclub/actions/workflows/ci.yml/badge.svg)](https://github.com/anishanilkumar/phpresellerclub/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/anishanilkumar/phpresellerclub/branch/master/graph/badge.svg)](https://codecov.io/gh/anishanilkumar/phpresellerclub)
[![Maintainability](https://qlty.sh/gh/anishanilkumar/projects/phpresellerclub/maintainability.svg)](https://qlty.sh/gh/anishanilkumar/projects/phpresellerclub)

A modern, dependency-injected PHP abstraction for the [ResellerClub](https://www.resellerclub.com/)
(LogicBoxes) HTTP API.

ResellerClub is one of the leading domain-name reseller systems, but their raw HTTP API is verbose
and error-prone to call directly. This library wraps it in a typed, testable client that works for
every reseller under it.

## Requirements

- PHP 8.2+
- ext-json, ext-intl (for IDN domain checks)
- Any [PSR-18](https://www.php-fig.org/psr/psr-18/) HTTP client (Guzzle is used by default)

## Installation

```bash
composer require anishanilkumar/phpresellerclub
```

## Usage

Create a `ResellerClub` client with your credentials and reach the API groups through its accessors.
The `Test` environment points at `test.httpapi.com` (ResellerClub's OT&E sandbox); switch to
`Environment::Production` when you go live.

```php
use Resellerclub\ResellerClub;
use Resellerclub\Config\Credentials;
use Resellerclub\Config\Environment;

$client = new ResellerClub(
    new Credentials('0000000', 'your-api-key', Environment::Test)
);

// Check availability
$result = $client->domains()->checkAvailability('example', ['com', 'net']);

// Register a domain
$client->domains()->register('example.com', [
    'years'             => 1,
    'ns'                => ['ns1.example.net', 'ns2.example.net'],
    'customer-id'       => '13647145',
    'reg-contact-id'    => '47738316',
    'admin-contact-id'  => '47738316',
    'tech-contact-id'   => '47738316',
    'billing-contact-id'=> '47738316',
    'invoice-option'    => 'NoInvoice',
]);

// Other groups
$client->contacts()->createContact([/* ... */]);
$client->customers()->getCustomerByCustomerId('13560800');
$client->billing()->getCustomerBalance('13560800');
```

### Bring your own HTTP client

The constructor accepts any PSR-18 client and PSR-17 factories, so you can plug in your own
transport, middleware, retries, or logging:

```php
use Resellerclub\ResellerClub;
use Resellerclub\Config\Credentials;

$client = new ResellerClub(
    new Credentials('0000000', 'your-api-key'),
    $myPsr18Client,       // Psr\Http\Client\ClientInterface
    $myRequestFactory,    // Psr\Http\Message\RequestFactoryInterface
    $myStreamFactory,     // Psr\Http\Message\StreamFactoryInterface
);
```

### Error handling

Every failure implements `Resellerclub\Exception\ResellerClubException`:

- `ApiConnectionException` — the request could not be completed (transport error or non-JSON body).
- `ValidationException` (and its subclasses `InvalidArrayException`, `InvalidItemException`,
  `InvalidParameterException`, `MissingParameterException`, `InvalidUrlArrayException`,
  `InvalidValidationException`) — the parameters were rejected before the request was sent.

```php
use Resellerclub\Exception\ResellerClubException;

try {
    $client->domains()->checkAvailability('example', 'com');
} catch (ResellerClubException $e) {
    // handle any library error
}
```

See the [`examples/`](examples/) directory for runnable scripts.

## Development

```bash
composer install
composer test        # PHPUnit + coverage
composer phpstan     # static analysis (level max)
composer cs:check    # coding standard (PSR-12)
composer check       # all of the above
```

## License

Released under the [GNU GPL v2 (or later)](LICENCE.txt).
