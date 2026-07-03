<?php

declare(strict_types=1);

namespace Resellerclub\Exception;

/**
 * Thrown when the API request cannot be completed at the transport level.
 *
 * Usual suspects: IP not whitelisted, no internet connection, TLS failure,
 * the API server is down, or a non-decodable response body.
 */
final class ApiConnectionException extends \RuntimeException implements ResellerClubException
{
}
