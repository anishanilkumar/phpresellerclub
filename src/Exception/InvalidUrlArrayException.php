<?php

declare(strict_types=1);

namespace Resellerclub\Exception;

/**
 * Thrown when a URL parameter cannot be encoded (e.g. a null or object value).
 */
final class InvalidUrlArrayException extends ValidationException
{
}
