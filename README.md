# Divar API

A classifieds marketplace backend (Divar-style) built with **Laravel 12**. It exposes a versioned REST API for mobile/web clients and a Persian admin panel for managing users, categories, advertisements, payments, and site settings.

## Features

### API (`/api/V1`)
- **Auth** — register/login with password, OTP login, password reset via OTP, profile management (Laravel Sanctum)
- **Cities** — list cities
- **Categories** — hierarchy, attributes, and attribute values
- **Advertisements** — browse, search, filter, view details, create ads (with images)
- **Payments & promotions** — ladder / special promotions via ZarinPal gateway

### Admin panel (`/admin`)
- Dashboard and statistics
- User, menu, category, and advertisement management
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
| Frontend (admin) | Blade, Tailwind CSS 4, Vite |
| Payments | ZarinPal |
| API docs | Admin Persian docs + Scramble OpenAPI (`/docs/api`) |
| Other | Eloquent Sluggable |

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL

## Installation

```bash
# Clone and enter the project
cd divar-api

# Install PHP dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Configure database in .env (see below), then:
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

For local development with Vite + queue:

```bash
composer run dev
```

## Environment variables

Key values from `.env.example` / `config/services.php`:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=divar_api
DB_USERNAME=root
DB_PASSWORD=

# ZarinPal
ZARINPAL_MERCHANT_ID=your-merchant-id
ZARINPAL_SANDBOX=true
ZARINPAL_CALLBACK_URL=http://127.0.0.1:8000/api/V1/payment/callback
```

SMS credentials are read from `config('sms.*')` (username, password, from, pattern_id). Add a `config/sms.php` (or equivalent) and matching `.env` keys before using OTP SMS in production.

## Useful URLs

| URL | Description |
|-----|-------------|
| `/login` | Admin login |
| `/admin` | Admin dashboard |
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
  Http/Controllers/Admin/   # Admin panel
  Http/Controllers/V1/      # Public API
  Http/Services/            # Ads, payments, SMS, uploads
  Models/                   # Eloquent models
  Support/                  # Helpers (e.g. JalaliDate)
resources/views/admin/      # Admin Blade UI
routes/api.php              # API routes
routes/web.php              # Admin + auth routes
database/migrations/        # Schema
database/seeders/           # Sample data
```

## Testing

```bash
composer test
# or
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
