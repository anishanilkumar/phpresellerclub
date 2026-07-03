<?php

declare(strict_types=1);

namespace Resellerclub\Config;

/**
 * Immutable ResellerClub API credentials.
 *
 * Replaces the former `RESELLER_ID` / `RESELLER_API_KEY` / `RESELLER_DOMAIN`
 * global constants. Because credentials are now an injectable value object, a
 * single process can talk to multiple reseller accounts or environments.
 */
final readonly class Credentials
{
  public function __construct(
    private string $authUserId,
    private string $apiKey,
    private Environment $environment = Environment::Test,
  ) {
  }

  public function authUserId(): string
  {
    return $this->authUserId;
  }

  public function apiKey(): string
  {
    return $this->apiKey;
  }

  public function environment(): Environment
  {
    return $this->environment;
  }

  /**
   * Authentication parameters injected into every API request.
   *
   * @return array{auth-userid: string, api-key: string}
   */
  public function toAuthParameters(): array
  {
    return [
      'auth-userid' => $this->authUserId,
      'api-key' => $this->apiKey,
    ];
  }
}
