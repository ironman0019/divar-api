# Divar API

A classifieds marketplace backend (Divar-style) built with **Laravel 12**. It exposes a versioned REST API for mobile/web clients and a Persian admin panel for managing users, categories, advertisements, payments, and site settings.

## Features

### API (`/api/V1`)
- **Auth** — register/login with password, OTP login, password reset via OTP, profile management (Laravel Sanctum)
- **OTP** — short-lived codes stored in **Redis** (120s TTL, cooldown, attempt limits); SMS sent via **queued jobs**
- **Cities** — list active cities (Redis-cached)
- **Categories** — hierarchy, attributes, and attribute values (Redis-cached with tag invalidation)
- **Advertisements** — browse, search, filter, view details, create ads (with images)
- **View counts** — buffered in Redis and flushed to MySQL on a schedule
- **Payments & promotions** — ladder / special promotions via ZarinPal gateway

### Admin panel (`/admin`)
- Dashboard, statistics, and **admin notifications**
- User, menu, category, **city**, and advertisement management
- **Promotion price** CRUD (ladder / special tariffs)
- Featured ads and promotion controls
- Income report and transactions (Jalali date filters)
- Site settings
- Persian API documentation page (`/admin/api-docs`)

## Tech stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 12, PHP 8.2+ |
| Auth (API) | Laravel Sanctum |
| Auth (Admin) | Session + admin middleware |
| Database | MySQL |
| Cache / OTP / counters | **Redis** (via `predis/predis`) |
| Queues | **Redis** (`SendOtpSmsJob`, etc.) |
| Frontend (admin) | Blade, Tailwind CSS 4, Vite |
| Payments | ZarinPal |
| API docs | Admin Persian docs + Scramble OpenAPI (`/docs/api`) |
| Other | Eloquent Sluggable |

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL
- **Redis** (local WSL, Docker, or server install)

## Installation

```bash
# Clone and enter the project
cd divar-api

# Install PHP dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Configure .env (database + Redis), then:
php artisan migrate
php artisan db:seed

# Frontend assets
npm install
npm run build

# Storage link (for uploaded images)
php artisan storage:link

# Run the app
php artisan serve
```

Or use the combined setup script:

```bash
composer run setup
```

### Start Redis

Redis must be **running** before cache, OTP, or queues work (installing it is not enough).

**WSL (recommended on Windows):**

```bash
sudo service redis-server start
redis-cli ping   # should return PONG
```

**Docker:**

```bash
docker run -d --name redis -p 6379:6379 redis:alpine
```

### Local development

`composer run dev` starts the HTTP server, **queue worker**, and Vite together:

```bash
composer run dev
```

For ad view flushing (Redis → MySQL), also run the scheduler in another terminal:

```bash
php artisan schedule:work
```

Or on a VPS, add a cron entry: `* * * * * php artisan schedule:run`

## Environment variables

Key values from `.env.example`:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=divar_api
DB_USERNAME=root
DB_PASSWORD=

# Redis (cache, OTP, queues, view counters)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ZarinPal
ZARINPAL_MERCHANT_ID=your-merchant-id
ZARINPAL_SANDBOX=true
ZARINPAL_CALLBACK_URL=http://127.0.0.1:8000/api/V1/payment/callback
```

SMS credentials are read from `config('sms.*')` (`SMS_USERNAME`, `SMS_PASSWORD`, `SMS_FROM`). OTP is sent as plain text via Melipayamak `SendSimpleSMS2` (no pattern ID). Set those `.env` keys before using OTP SMS in production.

## Redis usage in this project

Redis is not a replacement for MySQL. It handles **hot, short-lived, or high-write** data:

| Use case | Redis DB | Keys (logical) | Notes |
|----------|----------|----------------|-------|
| Catalog cache | 1 | `cities:all`, `categories:*`, `promotion-prices:active` | Invalidated on admin CRUD |
| OTP + cooldown | 0 | `otp:data:{token}`, `otp:register:{mobile}`, `otp:cooldown:*` | 120s OTP, 60s cooldown |
| SMS queue | 0 | `queues:default` | `SendOtpSmsJob` |
| Ad view buffer | 0 | `ad:views:{id}`, `ad:views:dirty` | Flushed by `ads:flush-views` every minute |
| Rate limiting | cache | Laravel `otp` limiter | Uses cache store |

Laravel prefixes keys (e.g. `laravel-database-otp:data:...`). Inspect in WSL:

```bash
redis-cli
SELECT 0          # OTP, views, queues
KEYS *otp*
GET laravel-database-otp:data:YOUR_TOKEN

SELECT 1          # cache
KEYS *cities*
```

Or via Tinker (uses logical key names):

```bash
php artisan tinker
>>> Cache::get('cities:all');
>>> Redis::keys('*otp*');
```

**Failed queue jobs** are stored in MySQL (`failed_jobs`), not Redis:

```bash
php artisan queue:failed
php artisan queue:retry {uuid}
```

OTP SMS jobs are time-sensitive; prefer automatic retries within the OTP TTL rather than manually retrying old failed jobs.

## Useful URLs

| URL | Description |
|-----|-------------|
| `/login` | Admin login |
| `/admin` | Admin dashboard |
| `/admin/cities` | City management |
| `/admin/payment/promotion-prices` | Promotion price tariffs |
| `/admin/api-docs` | Persian API documentation (admin) |
| `/docs/api` | Interactive Scramble OpenAPI docs |
| `/api/V1/...` | REST API base |

## API overview

Base URL: `{APP_URL}/api/V1`

Standard response envelope:

```json
{
  "status": "ok",
  "message": "...",
  "data": {}
}
```

Authenticated routes require:

```http
Authorization: Bearer {token}
Accept: application/json
```

### Main endpoint groups

| Group | Examples |
|-------|----------|
| Auth | `POST /auth/login`, `POST /auth/send-otp`, `GET /auth/profile` |
| Cities | `GET /cities` |
| Categories | `GET /categories`, `GET /categories/hierarchy`, `GET /categories/{id}/attributes` |
| Advertisements | `GET /advertisements`, `GET /advertisements/search?q=...`, `POST /advertisements` |
| Payments | `GET /payments/promotion-prices`, `POST /payments/promote-advertisement`, `GET /payment/callback` |

Full Persian documentation is available in the admin panel at **مستندات API**, and interactive docs at `/docs/api`.

## Database seeders

```bash
php artisan db:seed
```

Seeds cities, users, categories/attributes/values, sample advertisements, promotion prices, and payments.

### Test admin account

After seeding, you can sign in to the admin panel at `/login`:

| Field | Value |
|-------|-------|
| Mobile | `09000000000` |
| Password | `admin123` |

Regular seeded users use password `password` (see `UserSeeder`).

## Project structure (high level)

```
app/
  Console/Commands/         # e.g. ads:flush-views
  Http/Controllers/Admin/   # Admin panel (cities, promotion prices, …)
  Http/Controllers/V1/      # Public API
  Http/Services/            # Ads, payments, SMS, OTP, view counter
  Http/Services/Otp/        # Redis OTP service
  Jobs/                     # SendOtpSmsJob
  Support/                  # CatalogCache, JalaliDate, …
  Models/                   # Eloquent models
resources/views/admin/      # Admin Blade UI
routes/api.php              # API routes
routes/web.php              # Admin + auth routes
routes/console.php          # Scheduler (view flush)
database/migrations/        # Schema
database/seeders/           # Sample data
```

## Testing

```bash
composer test
# or
php artisan test
```

Ensure Redis is running if tests exercise cache/queue features.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
