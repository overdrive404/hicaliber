# Property Search (Laravel 12 + Vue 3)

## Requirements

- PHP 8.4
- Composer
- Node.js 18+ with npm
- SQLite (default) or another configured database

## Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
```

### Database

This project expects SQLite by default. The repository includes `database/database.sqlite`.

Run migrations + seeders (imports the CSV data into `properties`):

```bash
php artisan migrate --seed
```

## Frontend

```bash
npm install
npm run dev
```

## Run the app

```bash
php artisan serve
```

Open the app in your browser (default: `http://127.0.0.1:8000`).

## API

`GET /api/properties`

All parameters are optional:

- `name` (partial match)
- `bedrooms` (exact)
- `bathrooms` (exact)
- `storeys` (exact)
- `garages` (exact)
- `price_min` (min price)
- `price_max` (max price)

Example:

```bash
curl "http://127.0.0.1:8000/api/properties?bedrooms=4&bathrooms=2&price_min=300000&price_max=600000"
```
