# Hotel Inventory & Search System

A small but production-shaped hotel inventory and availability-search system built with
**Laravel 13**, **Sanctum**, **Blade + Bootstrap 5**, and a clean layered architecture
(**Controller → Service → Repository**).

It exposes both a **JSON API** (token auth) and a **Blade web UI** (session auth) that share
the exact same service layer — no duplicated business logic.

---

## Highlights

- **Date-aware availability** — availability is computed per date range from a real `bookings`
  table, not a static counter (see the design note below).
- **Layered architecture** — thin controllers, business logic in services, data access behind
  repository interfaces bound in a service provider.
- **Sanctum auth** — personal access tokens for the API, session guard for the web UI.
- **Form Request validation**, **API Resources**, correct **HTTP status codes**.
- **Cached search** with version-based invalidation that survives the file/array cache driver.
- **Rate limiting**, **overbooking-safe** booking writes (transaction + row lock).
- **21 tests** (unit + feature) green on SQLite in-memory.

---

## Tech stack

| Concern        | Choice                                            |
|----------------|---------------------------------------------------|
| Framework      | Laravel 13 (PHP 8.3+)                              |
| Auth           | Laravel Sanctum (tokens for API, session for web) |
| Database       | MySQL 8 (Docker); SQLite for the test suite       |
| Frontend       | Blade + Bootstrap 5 (CDN)                          |
| Cache          | Redis in Docker; file/array locally               |
| Code style     | PSR-12 via Laravel Pint                            |

---

## Architecture

```
HTTP (API / Web)
   │
Controllers  ──►  Form Requests (validation)
   │
Services        HotelService · RoomService · BookingService · SearchService
   │            (all business logic, availability algorithm, caching)
Repositories    *RepositoryInterface  ─bind→  Eloquent*Repository
   │
Eloquent Models  Hotel · Room · Booking · User
```

- Controllers only **validate → call a service → return a Resource/redirect**.
- Services depend on **repository interfaces**, injected via the constructor and bound to
  Eloquent implementations in `App\Providers\RepositoryServiceProvider`.
- The web controllers call the **same services** as the API controllers.

Key folders:

```
app/
  Http/Controllers/{Api,Web}/   Http/Requests/   Http/Resources/
  Services/   Services/Search/SearchResultCache.php
  Repositories/{Contracts,Eloquent}/
  Models/   Providers/RepositoryServiceProvider.php   Exceptions/
```

---

## Design note — date-aware availability (important)

The original brief ships a static `rooms.available_rooms` integer, but search takes
check-in/check-out dates and a static count cannot express *availability for a date range*.
We intentionally implemented the **production-correct** version:

- `rooms.total_rooms` holds the **physical inventory** of a room type (this replaces the static
  `available_rooms` column).
- A `bookings` table records each confirmed reservation (`room_id`, `checkin_date`,
  `checkout_date`, `guests`, `status`, `total_price`).
- Availability is **derived per requested range**, never stored.

**Overlap rule (half-open intervals `[checkin, checkout)` — the checkout day is free):**

```
existing.checkin_date < requested.checkout_date
AND existing.checkout_date > requested.checkin_date
```

**Available units** for a requested range:

```
available_units = total_rooms - MAX(overlapping confirmed bookings on any single night in the range)
```

`BookingService::availableUnits()` walks every night in the range, counts the confirmed
bookings covering that night, and subtracts the busiest night from `total_rooms`. Rooms with
`0` available units (or `max_occupancy < guests`) are excluded from results.

`total_price = price_per_night × nights`, where `nights = checkin.diffInDays(checkout)`.

---

## Caching & invalidation

Each search is cached for **60s** via `Cache::remember`, keyed by a hash of the normalised
query params (`SearchService` + `App\Services\Search\SearchResultCache`).

Invalidation uses a **monotonic version number** baked into the cache key rather than cache
**tags**, because the default `file`/`array` drivers do not support tagging. Creating a booking
calls `SearchResultCache::bump()`, which increments the version so all previously cached search
results are orphaned and recomputed on the next request (they also expire naturally on their
60s TTL).

> Running on **Redis** (as in the Docker setup) you could switch to real cache tags for more
> surgical invalidation; the version-key approach is the portable default.

---

## Getting started — local (quickest)

Requires PHP 8.3+ and Composer. Uses SQLite so no database server is needed.

```bash
composer install
cp -n .env.example .env          # already SQLite by default
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve                # http://127.0.0.1:8000
```

Log in at `/login` with the seeded admin (below).

## Getting started — Docker

Brings up `app` (PHP-FPM), `nginx` (port **8080**), `mysql` 8, and `redis`.

```bash
docker compose up -d --build
docker compose exec app cp -n .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
# open http://localhost:8080
```

A `Makefile` wraps these: `make up`, `make setup`, `make test`, `make pint`.

> The `app` service injects `DB_*`/`REDIS_*`/`CACHE_STORE` env vars pointing at the `mysql` and
> `redis` containers, so the committed `.env` (SQLite) is overridden inside Docker.

### Seeded credentials

| Email               | Password   |
|---------------------|------------|
| `admin@example.com` | `password` |

The seeder also creates ~9 hotels across Dubai, London, Paris and Tokyo with 2–4 room types
each and a spread of bookings so search results vary by date.

---

## API reference

Base prefix `/api`, JSON only. Protected routes need `Authorization: Bearer <token>`.

| Method | Endpoint        | Auth   | Description                                   |
|--------|-----------------|--------|-----------------------------------------------|
| POST   | `/api/login`    | public | Returns `{ token, user }` (401 on bad creds)  |
| POST   | `/api/logout`   | token  | Revokes the current token (204)               |
| GET    | `/api/hotels`   | public | Paginated list; filters `city`, `rating`, `per_page` |
| POST   | `/api/hotels`   | token  | Create a hotel (201)                          |
| POST   | `/api/rooms`    | token  | Create a room type (201)                      |
| GET    | `/api/search`   | public | Availability for `city` + date range + `guests` |
| POST   | `/api/bookings` | token  | Create a booking (201, 422 if unavailable)    |

**Rate limits:** `throttle:60,1` across the API group, `throttle:10,1` on `POST /api/login`.

### Example — search

```bash
curl "http://localhost:8080/api/search?city=Dubai&checkin_date=2026-07-01&checkout_date=2026-07-04&guests=2" \
  -H "Accept: application/json"
```

```json
{
  "data": [
    {
      "hotel": { "id": 1, "name": "Burj Marina Resort", "city": "Dubai", "country": "United Arab Emirates", "rating": 5 },
      "nights": 3,
      "rooms": [
        { "id": 1, "name": "Deluxe King", "price_per_night": "220.00", "max_occupancy": 2, "available_units": 5, "total_price": "660.00" }
      ]
    }
  ],
  "meta": { "checkin_date": "2026-07-01", "checkout_date": "2026-07-04", "guests": 2, "nights": 3 }
}
```

---

## Web pages

| Route        | Description                                              |
|--------------|---------------------------------------------------------|
| `/login`     | Session login form with inline validation               |
| `/dashboard` | Stats: total hotels, rooms, bookings, average rating    |
| `/hotels`    | Add hotel form + filterable, paginated list             |
| `/rooms`     | Add room form (hotel dropdown) + room list              |
| `/search`    | Availability search form + results with totals          |

---

## Tests

```bash
php artisan test
```

21 tests covering the availability algorithm (no bookings, fully booked, partial overlap,
half-open checkout day, occupancy filtering, pricing), auth (web redirect, API token,
401 on protected routes), and the booking flow (creation, 422 when full, cache invalidation).

Code style:

```bash
./vendor/bin/pint --test
```

---

## Postman

Import from `postman/`:

- `HotelInventory.postman_collection.json`
- `HotelInventory.postman_environment.json` (vars: `base_url`, `token`)

Run **Login** first — its test script saves the token into `{{token}}` for the protected
requests.

---

## What I'd add for production

- **Booking lifecycle**: cancellation endpoint (`status = cancelled`), guest/customer records,
  and emailed confirmations.
- **Stronger concurrency**: the booking write already uses a transaction + `lockForUpdate`;
  for high contention I'd add a unique constraint strategy or per-room serialization.
- **Redis cache tags** for targeted search invalidation instead of the version-key fallback.
- **Authorization policies / roles** (admin vs. staff) rather than "any authenticated user".
- **Observability**: structured logging, request IDs, and metrics on search latency.
- **API niceties**: cursor pagination, sorting, OpenAPI spec, idempotency keys on booking POST.
- **Static analysis** (PHPStan/Larastan level max) and CI running tests + Pint on every push.
