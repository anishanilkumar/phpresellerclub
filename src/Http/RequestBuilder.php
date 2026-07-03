<?php

declare(strict_types=1);

namespace Resellerclub\Http;

use Resellerclub\Config\Environment;
use Resellerclub\Exception\InvalidUrlArrayException;

/**
 * Builds request paths and query strings for the ResellerClub API.
 *
 * This is a pure, side-effect-free component (no network, no globals) that
 * ports the URL-building logic previously baked into the old `Core` class.
 */
final readonly class RequestBuilder
{
    private const string PROTOCOL = 'https';
    private const string FORMAT = 'json';

    public function __construct(
        private Environment $environment = Environment::Test,
    ) {
    }

    /**
     * Build the request path (URL without the query string).
     *
     * @param string      $section  Top-level API section, e.g. "domains".
     * @param string      $apiName  API method name, e.g. "available".
     * @param string|null $section2 Optional sub-section required by some calls.
     */
    public function buildPath(string $section, string $apiName, ?string $section2 = null): string
    {
        $host = $this->environment->host();
        $path = $section2 === null
            ? "$section/$apiName"
            : "$section/$section2/$apiName";

        return sprintf('%s://%s/api/%s.%s', self::PROTOCOL, $host, $path, self::FORMAT);
    }

    /**
     * Encode a parameter array into a `key=value&key=value` query string.
     *
     * Array values expand into repeated keys (`tlds=com&tlds=net`), matching
     * how the ResellerClub API expects list parameters.
     *
     * @param array<string, mixed> $parameters
     *
     * @throws InvalidUrlArrayException When a value cannot be URL-encoded.
     */
    public function buildQuery(array $parameters): string
    {
        $items = [];
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $items[] = $this->encode($key, $item);
                }
                continue;
            }
            $items[] = $this->encode($key, $value);
        }

        return implode('&', $items);
    }

    /**
     * Encode a single key/value pair, rejecting non-scalar values.
     *
     * @throws InvalidUrlArrayException
     */
    private function encode(string $key, mixed $value): string
    {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_int($value) || is_float($value)) {
            $value = (string) $value;
        }

        if (!is_string($value)) {
            throw new InvalidUrlArrayException(
                sprintf('Parameter "%s" has a value that cannot be encoded into a URL.', $key),
            );
        }

        return $key . '=' . urlencode($value);
    }
}
