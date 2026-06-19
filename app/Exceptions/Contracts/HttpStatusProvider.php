<?php

declare(strict_types=1);

namespace App\Exceptions\Contracts;

/**
 * Implemented by domain exceptions that map to a specific HTTP status code.
 *
 * The centralized {@see \App\Exceptions\ApiExceptionRenderer} reads this so the
 * status lives with the exception that knows it, instead of being re-decided at
 * every catch site.
 */
interface HttpStatusProvider
{
    /** The HTTP status code this exception should produce. */
    public function httpStatusCode(): int;
}
