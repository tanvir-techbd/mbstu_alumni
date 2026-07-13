# MBSTU Alumni Portal

A full Alumni Portal Management System — Laravel 12, Blade + Tailwind + Alpine.js, MySQL, role-based access via Spatie Permission. Full requirements and architecture live in [`.claude/DESIGN.md`](.claude/DESIGN.md); read [`.claude/CLAUDE.md`](.claude/CLAUDE.md) and [`.claude/PLAN.md`](.claude/PLAN.md) before picking up work.

## Stack

Laravel 12 · PHP 8.4+ · Blade · Tailwind CSS · Alpine.js · MySQL · Laravel Breeze · Spatie Laravel Permission · barryvdh/laravel-dompdf · Laravel Excel · Chart.js · Heroicons

## Prerequisites

- PHP 8.4+ with the usual Laravel extensions (`mbstring`, `pdo_mysql`, `xml`, `curl`, `zip`, `gd`, `bcmath`, `intl`, `fileinfo`, `tokenizer`)
- Composer 2.x
- Node.js 18+ / npm
- MySQL 8.x (or MariaDB) running locally

## Setup

```bash
composer install
cp .env.example .env   # already gitignored; .env is pre-filled for local dev
php artisan key:generate

# create the database (matches DB_DATABASE in .env)
mysql -u root -e "CREATE DATABASE mbstu_alumni"

php artisan migrate --seed
php artisan storage:link

npm install
npm run dev   # or `npm run build` for a production build
php artisan serve
```

Seeded accounts (local/dev only — see `database/seeders/DatabaseSeeder.php`, password for all is `password`):

| Role | Email |
|---|---|
| Super Admin | `admin@mbstu-alumni.test` |
| Alumni | `alumni@mbstu-alumni.test` |
| Student | `student@mbstu-alumni.test` |
| Faculty | `faculty@mbstu-alumni.test` |

## Project layout

```
app/                     Laravel application code (Models, Services, Policies, Http/Requests, Enums, ...)
database/                Migrations, seeders, factories
resources/views/         Blade views (layouts, components, pages)
routes/                  web.php (+ auth.php from Breeze)
static_prototype_folder/ Static HTML/Tailwind-CDN mockups — design pages here before porting to Blade
.claude/                 Project memory: CLAUDE.md (read first), DESIGN.md (architecture, ERD), PLAN.md (roadmap + progress log)
```

## Testing

```bash
php artisan test
```

## Development workflow

This project ships in small, reviewable milestones — see [`.claude/PLAN.md`](.claude/PLAN.md) for the roadmap and the per-milestone checklist (schema explained before code, migration → model → policy → form request → controller → routes → Blade view → seeder/factory → manual test pass).
