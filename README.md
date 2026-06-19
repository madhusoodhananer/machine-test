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
- **UUID v4** primary keys, **soft deletes**, and an **audit trail** (`created_by` / `updated_by` /
  `deleted_by`) on every inventory table via a shared base model.
- **25 tests** (unit + feature) green on SQLite in-memory.

---

## Tech stack

| Concern        | Choice                                                      |
|----------------|-------------------------------------------------------------|
| Framework      | Laravel 13 (PHP 8.4)                                         |
| Auth           | Laravel Sanctum (tokens for API, session for web)           |
| Database       | MySQL 8 (host or Sail); SQLite in-memory for the test suite |
| Keys           | UUID v4 primary keys + soft deletes + audit columns         |
| Frontend       | Blade + Bootstrap 5, Tom Select & Bootstrap Icons (CDN)     |
| Cache          | Redis in Sail; database/file driver locally                 |
| Container      | Laravel Sail (Docker)                                       |
| Code style     | PSR-12 via Laravel Pint                                      |

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

The brief models availability as a static `rooms.available_rooms` integer, which is a clean
and simple starting point. Since search accepts check-in/check-out dates, I extended that idea
so availability can be expressed **for a specific date range** rather than as a single number.
This keeps the brief's intent and adds date-awareness on top:

- `rooms.total_rooms` holds the **physical inventory** of a room type (the date-aware count is
  derived from this instead of being stored directly).
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

There are two supported ways to run the app:

1. **Normal (no Docker)** — host PHP + a MySQL server you already run locally.
2. **Sail (Docker)** — everything (PHP 8.4, MySQL 8, Redis) in containers.

Both end at the same place: the web UI at the printed URL and the JSON API under `/api`.

---

## Getting started — Normal (no Docker)

Run directly on your machine. Good when you already have PHP and a MySQL server (e.g. Herd,
DBngin, or a local Docker MySQL) listening on `127.0.0.1:3306`.

**Requirements**

- PHP **8.4** with the usual Laravel extensions (`pdo_mysql`, `mbstring`, `openssl`, `bcmath`,
  `ctype`, `fileinfo`, `tokenizer`, `xml`).
- Composer 2.
- A reachable **MySQL 8** server and credentials.

**1 — Install dependencies and create the env file**

```bash
composer install
cp -n .env.example .env
php artisan key:generate
```

**2 — Point `.env` at your MySQL server**

`.env.example` defaults to SQLite. For the normal MySQL run, set these keys in `.env`
(use your own MySQL username and password):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_hotel
DB_USERNAME=root
DB_PASSWORD=your_password
```

> Prefer zero setup? Leave `.env.example`'s `DB_CONNECTION=sqlite`, then
> `touch database/database.sqlite` and skip the database-creation step below.

**3 — Create the database (once)**

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p -e "CREATE DATABASE IF NOT EXISTS db_hotel"
```

**4 — Migrate, seed, and serve**

```bash
php artisan config:clear
php artisan migrate:fresh --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Open **http://127.0.0.1:8000** and log in at `/login` with the seeded admin (below).

---

## Getting started — Sail (Docker)

Laravel Sail brings up three containers defined in `docker-compose.yml`:

| Service        | Image                  | Purpose                  | Host port (from `.env`) |
|----------------|------------------------|--------------------------|-------------------------|
| `laravel.test` | `sail-8.4/app`         | PHP 8.4 app server       | `APP_PORT` → **8088**   |
| `mysql`        | `mysql/mysql-server:8` | MySQL 8 database         | `FORWARD_DB_PORT` → **3307** |
| `redis`        | `redis:alpine`         | Cache / queue            | `FORWARD_REDIS_PORT` → **6380** |

The host ports are forwarded (8088/3307/6380) so they don't clash with a MySQL/Redis you may
already run on the standard ports.

**1 — Create the env file**

```bash
cp -n .env.example .env
```

Make sure these keys are set for Sail (the committed `.env` already uses them):

```dotenv
APP_PORT=8088
DB_CONNECTION=mysql
DB_HOST=mysql            # the service name inside the Docker network
DB_PORT=3306
DB_DATABASE=db_hotel
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=3307
FORWARD_REDIS_PORT=6380
```

> `DB_HOST=mysql` resolves **inside** the Docker network. From your host shell that name does
> not resolve — that's why artisan/migrate commands below run **through** Sail
> (`./vendor/bin/sail artisan ...`), not directly.

**2 — Build and start the containers**

```bash
./vendor/bin/sail up -d --build
```

**3 — Key, migrate, and seed (inside the container)**

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed
```

Open **http://localhost:8088** and log in at `/login`.

**Handy Sail commands**

```bash
./vendor/bin/sail artisan test       # run the test suite
./vendor/bin/sail artisan tinker     # REPL
./vendor/bin/sail mysql              # mysql shell on the db container
./vendor/bin/sail down               # stop and remove containers
```

> A short-alias tip: `alias sail='./vendor/bin/sail'` lets you drop the `./vendor/bin/` prefix.

---

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
# Sail: port 8088 · Normal run: port 8000
curl "http://localhost:8088/api/search?city=Dubai&checkin_date=2026-07-01&checkout_date=2026-07-04&guests=2" \
  -H "Accept: application/json"
```

```json
{
  "data": [
    {
      "hotel": { "id": "9b1c…uuid", "name": "Burj Marina Resort", "city": "Dubai", "country": "United Arab Emirates", "rating": 5 },
      "nights": 3,
      "rooms": [
        { "id": "7f2a…uuid", "name": "Deluxe King", "price_per_night": "220.00", "max_occupancy": 2, "available_units": 5, "total_price": "660.00" }
      ]
    }
  ],
  "meta": { "checkin_date": "2026-07-01", "checkout_date": "2026-07-04", "guests": 2, "nights": 3 }
}
```

> IDs are **UUID v4** strings (not integers).

---

## Web pages

| Route        | Description                                              |
|--------------|---------------------------------------------------------|
| `/login`     | Session login form with inline validation               |
| `/dashboard` | Stats: total hotels, rooms, bookings, average rating    |
| `/hotels`    | Add/edit/delete hotels (modals) + searchable city filter + paginated list |
| `/rooms`     | Add/edit/delete rooms (searchable hotel dropdown) + search + pagination    |
| `/bookings`  | Create/delete bookings with validation + paginated list |
| `/search`    | Availability search form + results with totals          |

Deletes are dependency-checked: a hotel with rooms, or a room with bookings, cannot be
removed and the UI shows a clear message instead.

---

## Tests

```bash
php artisan test                 # normal run (host)
./vendor/bin/sail artisan test   # inside Sail
```

The suite always runs on **SQLite in-memory** (configured in `phpunit.xml`), so it never
touches your MySQL data and needs no database setup.

25 tests covering the availability algorithm (no bookings, fully booked, partial overlap,
half-open checkout day, occupancy filtering, pricing), auth (web redirect, API token,
401 on protected routes), the booking flow (creation, 422 when full, cache invalidation),
and the audit-trail / soft-delete behaviour on the base model.

Code style:

```bash
./vendor/bin/pint --test
```

---

## Postman

Import both files from the project root:

- `HotelInventory.postman_collection.json`
- `HotelInventory.postman_environment.json` (vars: `base_url`, `token`, `hotel_id`, `room_id`)

`base_url` defaults to `http://localhost:8000` (the no-Docker run); change it to
`http://localhost:8088` if you run via Sail.

Run the requests in order — **Login → Create Hotel → Create Room → Create Booking**. Their
test scripts capture the returned token and the UUIDs into `{{token}}`, `{{hotel_id}}` and
`{{room_id}}`, so the chain works without pasting IDs by hand.

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
