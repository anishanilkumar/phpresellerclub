<?php

declare(strict_types=1);

namespace Resellerclub\Config;

/**
 * The ResellerClub API environment a request is issued against.
 *
 * Replaces the former `RESELLER_DOMAIN` global constant.
 */
enum Environment: string
{
    /** Test / OT&E environment — use this while developing. */
    case Test = 'test.httpapi.com';

    /** Live environment — real orders and real money. */
    case Production = 'httpapi.com';

    /**
     * The host name used to build the API base URL.
     */
    public function host(): string
    {
        return $this->value;
    }
}
