<?php

declare(strict_types=1);

namespace Resellerclub\Exception;

/**
 * Base class for every validation error raised before a request is sent.
 */
class ValidationException extends \InvalidArgumentException implements ResellerClubException
{
}
