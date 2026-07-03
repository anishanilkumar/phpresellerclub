<?php

declare(strict_types=1);

namespace Resellerclub\Http;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Resellerclub\Config\Credentials;
use Resellerclub\Exception\ApiConnectionException;

/**
 * Sends requests to the ResellerClub API over any PSR-18 HTTP client.
 *
 * Replaces the hardcoded cURL logic of the old `Core::callApi()`. Because the
 * transport is injected, calls are fully mockable in tests and the library is
 * not tied to a specific HTTP implementation.
 */
final readonly class ApiClient
{
  public function __construct(
    private ClientInterface $httpClient,
    private RequestFactoryInterface $requestFactory,
    private StreamFactoryInterface $streamFactory,
    private Credentials $credentials,
    private RequestBuilder $requestBuilder,
  ) {
  }

  /**
   * Execute an API call and return the decoded JSON response.
   *
   * @param array<string, mixed> $parameters
   *
   * @throws ApiConnectionException When the request fails or the response is not valid JSON.
   */
  public function send(
    HttpMethod $method,
    string $section,
    string $apiName,
    array $parameters = [],
    ?string $section2 = null,
  ): mixed {
    $path = $this->requestBuilder->buildPath($section, $apiName, $section2);
    $query = $this->requestBuilder->buildQuery(
      $this->credentials->toAuthParameters() + $parameters,
    );

    if ($method === HttpMethod::Get) {
      $target = $query === '' ? $path : "$path?$query";
      $request = $this->requestFactory->createRequest('GET', $target);
    } else {
      $request = $this->requestFactory->createRequest('POST', $path)
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->withBody($this->streamFactory->createStream($query));
    }

    try {
      $response = $this->httpClient->sendRequest($request);
    } catch (ClientExceptionInterface $e) {
      throw new ApiConnectionException('Cannot connect to the ResellerClub API server.', 0, $e);
    }

    return $this->decode((string) $response->getBody());
  }

  /**
   * @throws ApiConnectionException
   */
  private function decode(string $body): mixed
  {
    try {
      return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {
      throw new ApiConnectionException('The API returned a response that is not valid JSON.', 0, $e);
    }
  }
}
