<?php

declare(strict_types=1);

namespace App\Services\Search;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Caches search results for a short TTL.
 *
 * Invalidation uses a monotonically increasing "version" baked into the cache
 * key rather than cache tags, so it works on the default file/array cache
 * drivers (which do not support tagging). Booking writes call {@see bump()},
 * which advances the version and effectively orphans every previously cached
 * search result (they expire naturally on their own TTL).
 */
class SearchResultCache
{
    private const VERSION_KEY = 'search:version';

    private const TTL_SECONDS = 60;

    /**
     * @param  Closure(): array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    public function remember(string $fingerprint, Closure $callback): array
    {
        $key = sprintf('search:%d:%s', $this->version(), $fingerprint);

        return Cache::remember($key, self::TTL_SECONDS, $callback);
    }

    /**
     * Advance the cache version so existing search results are no longer read.
     */
    public function bump(): void
    {
        Cache::add(self::VERSION_KEY, 1); // seed if missing
        Cache::increment(self::VERSION_KEY);
    }

    private function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }
}
