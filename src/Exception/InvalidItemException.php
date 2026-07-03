<?php

declare(strict_types=1);

namespace Resellerclub\Exception;

/**
 * Thrown when an individual item fails its dedicated validator (e.g. a malformed email).
 */
final class InvalidItemException extends ValidationException
{
}
