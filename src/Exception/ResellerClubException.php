<?php

declare(strict_types=1);

namespace Resellerclub\Exception;

/**
 * Marker interface implemented by every exception thrown by this library.
 *
 * Catch this to handle any ResellerClub error in a single block.
 */
interface ResellerClubException extends \Throwable
{
}
