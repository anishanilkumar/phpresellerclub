<?php

declare(strict_types=1);

namespace Resellerclub\Exception;

/**
 * Thrown when an array contains a parameter that is neither mandatory nor optional for the call.
 */
final class InvalidParameterException extends ValidationException
{
}
