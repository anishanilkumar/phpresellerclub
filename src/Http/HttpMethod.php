<?php

declare(strict_types=1);

namespace Resellerclub\Http;

/**
 * HTTP verb used for an API call.
 *
 * Replaces the former `METHOD_GET` / `METHOD_POST` global constants. The
 * ResellerClub API requires POST for anything that mutates state and accepts
 * GET for read-only calls.
 */
enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
}
