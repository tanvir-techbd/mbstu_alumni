# MBSTU Alumni Portal — Project Memory

Read this file, `DESIGN.md`, and `PLAN.md` at the start of every session in this repo before writing code.

## What this is

A full Alumni Portal Management System for a university (MBSTU). Laravel 12 / PHP 8.4+ backend, Blade + Tailwind + Alpine.js frontend, MySQL, RBAC via Spatie Permission. Full requirements, schema, and architecture: [DESIGN.md](DESIGN.md). Roadmap and progress log: [PLAN.md](PLAN.md).

## The one hard rule

**Before implementing any module, explain the architecture and show the database schema for that module first**, then build it. Don't jump straight to code for a new module. Ship in small milestones, not one huge diff — see `PLAN.md` for the intended order and update its checkboxes + progress log as milestones land.

## Tech stack (installed, don't re-pick)

- Laravel 12, PHP 8.5 (see environment quirks below)
- Blade + Tailwind CSS v3 + Alpine.js (Breeze `blade` stack, installed with `--dark`)
- MySQL (`DB_CONNECTION=mysql` in `.env`)
- Auth: Laravel Breeze (login, register, forgot/reset password, email verification, profile)
- Authorization: `spatie/laravel-permission` — roles are `super-admin`, `alumni`, `student`, `faculty` (`App\Enums\RoleName`)
- PDF: `barryvdh/laravel-dompdf`
- Excel: `maatwebsite/excel`
- Charts: Chart.js (not yet added to `package.json` — add when the first dashboard chart is built)
- Icons: Heroicons (used inline as SVG in Blade so far — no npm package needed for that)

## Code conventions

- Controllers: resource controllers, thin — business logic goes in a service class under `app/Services/`, not the controller.
- Validation: dedicated `FormRequest` classes under `app/Http/Requests/`, never inline `$request->validate()` for anything beyond trivial one-off cases.
- Authorization: Policies under `app/Policies/`, registered via `AuthServiceProvider` or auto-discovery — don't scatter `if (! $user->hasRole(...))` checks through controllers.
- Every module that has a workflow with states (verification, job approval, mentorship, success stories, feedback tickets) should model that state as an enum, not a raw string column with implicit values. Follow the pattern in `App\Enums\RoleName`.
- Reusable Blade UI goes in `resources/views/components/`; don't duplicate markup across views.
- No placeholder/stub implementations — every route that's added should work end to end (migration → model → policy → controller → view).

## No Docker, ever

The user does not know Docker and does not want it used — this is a standing preference, not just a sandbox limitation. Local dev runs on **LAMPP** (XAMPP for Linux): MySQL reachable at `127.0.0.1:3306`, database `mbstu_alumni`. Never suggest `docker`, `docker compose`, or `laravel/sail` commands/workflows, even though `laravel/sail` ships in `composer.json` require-dev by default from `laravel/laravel` — it's unused dead weight, not an invitation to use it. Explain setup/run steps in terms of LAMPP (start Apache/MySQL from the XAMPP control panel or `sudo /opt/lampp/lampp start`, `php artisan serve` for the app itself) instead.

## Environment quirks (this sandbox only — irrelevant on the user's real dev machine)

- **PHP 8.5 has no native `ext-mbstring`** in this container and there's no passwordless sudo to install it. `mb_*` functions are only available because `phpoffice/phpspreadsheet` (a transitive dependency of `maatwebsite/excel`) pulls in `symfony/polyfill-mbstring`. If that dependency is ever removed, `composer require` and `artisan` commands will need `--ignore-platform-req=ext-mbstring` again and a polyfill dependency re-added. Don't "fix" this by weakening `composer.json` platform requirements — the target deploy machine should have real `ext-mbstring`.
- **MySQL is available via the user's LAMPP install**, reachable on `127.0.0.1:3306`; `.env` (`DB_DATABASE=mbstu_alumni`) is a real, migrated, seeded database as of 2026-07-14 — `php artisan migrate` / `db:seed` work fine now. There's no `mysql` CLI client in this container, so use `php artisan tinker` or Laravel's `DB` facade for ad-hoc queries instead of shelling out to `mysql`.
- `maatwebsite/excel` required `--ignore-platform-req=php` at install time because `phpoffice/phpspreadsheet`'s published constraint caps at `<8.5.0` even though it runs fine on 8.5 in practice. Re-check this constraint when bumping the lock file.
- **No `ext-curl`** either — Composer itself warns about this ("operating significantly slower... you do not have the PHP curl extension enabled") and falls back to streams. Slower but functional; not worth chasing without sudo.

## Where things are

- `static_prototype_folder/` — throwaway static HTML/Tailwind-CDN/Alpine-CDN mockups, designed and approved *before* porting to Blade. See its own README for the workflow.
- `.claude/DESIGN.md` — full requirements, database ERD, and architecture decisions.
- `.claude/PLAN.md` — milestone roadmap + living progress log; update it at the end of each milestone.
