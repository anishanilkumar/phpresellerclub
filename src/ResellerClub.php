<?php

declare(strict_types=1);

namespace Resellerclub;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Resellerclub\Api\Billing;
use Resellerclub\Api\Contact;
use Resellerclub\Api\Customer;
use Resellerclub\Api\Domain;
use Resellerclub\Config\Credentials;
use Resellerclub\Http\ApiClient;
use Resellerclub\Http\RequestBuilder;
use Resellerclub\Validation\Validator;

/**
 * Entry point for the library.
 *
 * Wire it up with your {@see Credentials} and, optionally, any PSR-18 HTTP
 * client. When no client is supplied, a Guzzle client is used by default.
 *
 * ```php
 * $rc = new ResellerClub(new Credentials('0000000', 'api-key', Environment::Test));
 * $rc->domains()->checkAvailability('example', 'com');
 * ```
 */
final class ResellerClub
{
    private readonly ApiClient $apiClient;

    private Domain $domain;
    private Contact $contact;
    private Customer $customer;
    private Billing $billing;

    public function __construct(
        Credentials $credentials,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
    ) {
        $factory = new HttpFactory();

        $this->apiClient = new ApiClient(
            $httpClient ?? new GuzzleClient(),
            $requestFactory ?? $factory,
            $streamFactory ?? $factory,
            $credentials,
            new RequestBuilder($credentials->environment()),
        );
    }

    public function domains(): Domain
    {
        return $this->domain ??= new Domain($this->apiClient, new Validator());
    }

    public function contacts(): Contact
    {
        return $this->contact ??= new Contact($this->apiClient, new Validator());
    }

    public function customers(): Customer
    {
        return $this->customer ??= new Customer($this->apiClient, new Validator());
    }

    public function billing(): Billing
    {
        return $this->billing ??= new Billing($this->apiClient, new Validator());
    }
}
