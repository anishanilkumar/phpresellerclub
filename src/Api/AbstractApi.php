<?php

declare(strict_types=1);

namespace Resellerclub\Api;

use Resellerclub\Http\ApiClient;
use Resellerclub\Http\HttpMethod;
use Resellerclub\Validation\Validator;

/**
 * Base class for the API groupings (domains, contacts, customers, billing).
 *
 * Holds the shared {@see ApiClient} and {@see Validator} and exposes a thin
 * {@see AbstractApi::call()} helper. Replaces the old inheritance from `Core`.
 */
abstract class AbstractApi
{
    public function __construct(
        protected readonly ApiClient $client,
        protected readonly Validator $validator,
    ) {
    }

    /**
     * Validate (optionally) and dispatch an API call.
     *
     * @param array<string, mixed> $parameters
     */
    protected function call(
        HttpMethod $method,
        string $section,
        string $apiName,
        array $parameters = [],
        ?string $section2 = null,
    ): mixed {
        return $this->client->send($method, $section, $apiName, $parameters, $section2);
    }
}
