# Plan

Roadmap + living progress log. For *what*/*why* (requirements, schema, architecture), see `DESIGN.md`.

---

## Roadmap

Suggested build order — dependency-driven (Directory needs Profiles; Notifications/Reports/Activity Log are easiest to wire in once there are real events to hook into). Adjust freely; tick the checkbox and add an entry to the progress log below when a milestone lands.

- [x] **M0 — Project scaffolding.** Laravel 12 + Breeze (blade, dark mode) + Spatie Permission + dompdf + Laravel Excel installed. Base roles seeded (`super-admin`, `alumni`, `student`, `faculty`). `users` table extended with `phone`, `status`, `profile_photo_path`. Dark/light mode toggle wired (Alpine store + no-FOUC script). `static_prototype_folder/` and `.claude/` set up.
- [x] **M1 — Role-based dashboard shell.** Sticky sidebar + topbar layout (replaces Breeze's simple top nav), role-aware sidebar nav, breadcrumbs, `DashboardController` dispatches to one view per role with stat-card placeholders (real counts where `users`/roles data already exists, honest "Available after M-x" placeholders elsewhere).
- [x] **M2 — User Management.** Admin CRUD on users under `/admin/users`, role assignment, activate/deactivate, search/filter/paginate, delete guarded against self-delete and deleting the last super-admin.
- [ ] **M3 — Alumni Profile + Verification workflow.** `alumni_profiles` table, profile form (personal/academic/professional/social/skills/bio), document upload, pending→approved/rejected admin review flow.
- [ ] **M4 — Alumni Directory.** Public search/sort over verified alumni only.
- [ ] **M5 — Events.** CRUD + publish/archive, registration, capacity, participant export, attendance marking.
- [ ] **M6 — Job Portal.** Post → pending approval → published workflow, browse/search/bookmark.
- [ ] **M7 — Mentorship.** Request → accept/reject → scheduled → completed.
- [ ] **M8 — Notice Board.** Notice/circular/scholarship/news/announcement, attachments, bookmarks.
- [ ] **M9 — Success Stories.** Submit → admin approval → published.
- [ ] **M10 — Donations.** Campaigns, donate, history, receipts, admin reports.
- [ ] **M11 — Gallery.** Albums by category, lazy-loaded image preview.
- [ ] **M12 — Documents.** Categorized repository, secure download.
- [ ] **M13 — Feedback.** Suggestions/complaints/feature requests, admin reply/close, export.
- [ ] **M14 — Notifications.** In-app + email, wired into triggers from M3/M5/M6/M7/M10.
- [ ] **M15 — Reports.** PDF/Excel export for alumni list, events, jobs, donations, verification status.
- [ ] **M16 — Activity Log.** Login/logout, profile update, job creation, event registration, donation, approval actions.
- [ ] **M17 — Global Search.** Alumni, jobs, events, notices, documents.
- [ ] **M18 — Final polish.** Toasts, loading skeletons, empty states, confirmation dialogs, responsive + dark/light QA pass, rate-limiting/security review across all modules.

### Per-milestone checklist (apply to every module milestone)

1. Explain architecture + DB schema *before* writing code
2. Migration files
3. Models + relationships
4. Policy (authorization)
5. Form Request(s) (validation)
6. Controller (resource, thin — logic in a Service class)
7. Routes
8. Blade views (built from a `static_prototype_folder/` mockup where UI is non-trivial)
9. Seeder + Factory
10. Manual test pass (the `/verify` skill or equivalent — drive the actual flow, don't just eyeball the code)

---

## Progress log

Newest entry first. One entry per milestone/session — what shipped, what's next, anything surprising. Read this before re-deriving context from scratch.

### 2026-07-14 — M2: User Management

**Done**
- `app/Http/Controllers/Controller.php`: added the `AuthorizesRequests` trait back (Laravel 12's default base `Controller` ships empty) so `$this->authorize()` works.
- `App\Policies\UserPolicy`: `viewAny`/`view`/`create`/`update` require `super-admin`; `delete`/`toggleStatus` additionally block deleting/deactivating yourself or the last remaining `super-admin`.
- `App\Http\Requests\Admin\{Store,Update}UserRequest`: own `authorize()` (`$this->user()->can(...)`) so create/update are policy-gated without needing `authorizeResource()`.
- `App\Services\UserManagementService`: create/update/toggleStatus/delete — password hashing and role sync live here, not in the controller.
- `App\Http\Controllers\Admin\UserController` (resource, `except('show')` — no standalone detail page, edit covers it) under `routes/admin.php`, `prefix('admin')->middleware(['auth','verified','role:super-admin'])`.
- `admin/users/{index,create,edit,_form}.blade.php` — searchable/filterable/paginated table, activate/deactivate, delete via Breeze's existing `x-modal` confirm pattern (per-row named modal).
- New reusable components: `x-toast` (session-flash success/error, mounted once in `layouts/app.blade.php`), `x-empty-state`.
- Sidebar's "Users" link now points at `admin.users.index` for `super-admin` instead of the `#`/Soon placeholder.
- `DatabaseSeeder`: added 12 alumni + 8 students + 5 faculty + 4 inactive alumni via factory, so the list/search/filter/pagination UI has real data to exercise (33 users total after seeding).

**Bugs caught during verification (both fixed, not just noted)**
1. `authorizeResource()` in the controller constructor threw `Call to undefined method UserController::middleware()` — Laravel 12's slimmed base `Controller` doesn't have the old instance-method `middleware()` that trait relies on internally. Fixed by dropping `authorizeResource()` and calling `$this->authorize()` explicitly per action instead (more idiomatic for 12 anyway).
2. `admin/users/_form.blade.php` threw `Undefined variable $user` on the **create** page (no `$user` passed): `old('role', $user->roles->first()?->name ?? '')` chains a method call (`->first()`) after the possibly-undefined `$user`, and PHP's `??`/nullsafe notice-suppression only covers *simple* variable/property access, not once a method call is chained in. Fixed by normalizing at the top of the partial: `$user = $user ?? null;` (safe — bare variable access) then using `$user?->` throughout and a precomputed `$currentRole` local.

**Verified — full HTTP click-through against the real LAMPP-backed `mbstu_alumni` DB** (migrate:fresh --seed, `php artisan serve`, cookie-jar curl through actual middleware, not shortcuts):
- Index: 33 total, 15/page pagination correct (16 `<tr>` = 1 header + 15 rows).
- Search (`?search=Demo`) returned exactly the 3 "Demo *" seeded users.
- Filter (`?role=alumni&status=inactive`) returned exactly the 4 seeded inactive alumni.
- Create → new user persisted with the correct role.
- Edit/update → name, role changed and persisted correctly.
- Toggle-status → flipped active→inactive correctly.
- Delete → row actually removed from the DB.
- A `student`-role user hit `/admin/users` and got **403** (route-level `role:super-admin` middleware).
- The `super-admin` tried to delete **themselves** and got **403** (policy guard) — account still exists afterward, confirmed via `tinker`.
- Reset to a clean `migrate:fresh --seed` afterward so the DB isn't left in the test-mutated state.

**Next milestone:** M3 — Alumni Profile + Verification workflow (`alumni_profiles` table, personal/academic/professional/social/skills/bio fields, document upload, pending→approved/rejected admin review).

### 2026-07-14 — M1: Role-based dashboard shell

**Done**
- `bootstrap/app.php`: registered Spatie's `role`/`permission`/`role_or_permission` middleware aliases (Laravel 11+ style, no `Kernel.php`).
- `resources/js/app.js`: added `Alpine.store('sidebar')` (open/toggle) alongside the existing `darkMode` store.
- `resources/views/layouts/app.blade.php` (the `<x-app-layout>` shell used by every authenticated page) rebuilt as sticky sidebar + topbar + optional `$breadcrumbs`/`$header` slots — ported directly from `static_prototype_folder/pages/dashboard/admin.html`, same Tailwind tokens and Alpine stores.
- New components: `x-sidebar-nav` (role-aware via `@role()`), `x-sidebar-link`, `x-breadcrumbs`, `x-stat-card`, `x-stat-card-placeholder`.
- Retired `layouts/navigation.blade.php`, `components/nav-link.blade.php`, `components/responsive-nav-link.blade.php` — dead code now that the sidebar replaces Breeze's top nav; confirmed no other view referenced them before deleting.
- `App\Http\Controllers\DashboardController@index` resolves the user's role (`App\Enums\RoleName`) and renders `dashboard.{admin,alumni,student,faculty}`; unrecognized/no role throws a 403 rather than silently guessing. Admin dashboard queries real counts (`User::role(...)->count()`) since `users`+Spatie tables already exist; every other card is an honest `x-stat-card-placeholder` naming the milestone that will back it (no fabricated numbers).
- `routes/web.php`: `/dashboard` now points at `DashboardController@index` instead of a closure returning a single shared view.
- `resources/views/profile/edit.blade.php` updated to the new layout slots (breadcrumbs + header), dropped its now-redundant `max-w-7xl` wrapper since `<main>` in the new layout already applies page padding.

**Verified**
- `npm run build` clean.
- `php artisan view:cache` compiled every Blade template (including all new components/views) with no syntax errors, then cleared again for local dev.
- `php -l` clean on all new/changed PHP files.
- `php artisan route:list` confirms `/dashboard` → `DashboardController@index`.
- **Full logged-in click-through, done the same day once the user pointed this sandbox at their LAMPP MySQL** (`mbstu_alumni` DB, port 3306 reachable — this sandbox has no Docker, so LAMPP's MySQL is the real backing DB here, not just for the user's separate machine): `php artisan migrate --force` (4 migrations, clean) → `php artisan db:seed --force` → started `php artisan serve`, logged in as all 4 seeded users via real HTTP requests (cookie-jar curl, following the actual `auth`+`verified` middleware, not a tinker shortcut), fetched `/dashboard` for each. All 4 returned 200 with zero server-error strings in the HTML. Confirmed: sidebar section headers differ correctly per role (super-admin gets Management/Content/Finance, alumni gets Alumni/Community, student gets Student/Resources, faculty gets Faculty/Resources — all get Overview+Account); admin dashboard's real stat cards show the correct seeded counts (1 alumni, 1 student, 1 faculty, 4 total users); breadcrumb renders "Admin / Dashboard"; built CSS/JS assets return 200. Not yet checked in an actual browser: dark-mode toggle click behavior and mobile sidebar open/close animation (HTML/JS is present and correct per the earlier Blade-compile check, but nobody has clicked it) — low risk, worth a 30-second look next time this is opened in a browser.

**Next milestone:** M2 — User Management (admin CRUD, role assignment, activate/deactivate, search/filter/paginate).

### 2026-07-14 — M0: Project scaffolding

**Done**
- Laravel 12 (PHP 8.5.4) installed at repo root, git initialized.
- Breeze `blade` stack installed with `--dark` (Blade + Tailwind v3 + Alpine.js).
- Installed: `spatie/laravel-permission`, `barryvdh/laravel-dompdf`, `maatwebsite/excel` (+ configs published: `config/permission.php`, `config/dompdf.php`, `config/excel.php`).
- `users` table (pre-migration, edited in place — safe since it hadn't run yet) extended with `phone`, `profile_photo_path`, `status` enum (`active`/`inactive`).
- `App\Models\User` implements `MustVerifyEmail`, uses `HasRoles`.
- `App\Enums\RoleName` (backed enum: `super-admin`, `alumni`, `student`, `faculty`) + `RoleSeeder` + `DatabaseSeeder` seeds one demo user per role (`admin@mbstu-alumni.test` / `alumni@...` / `student@...` / `faculty@...`, password `password` — **local/dev only, do not ship this seeder as-is to production**).
- Dark/light mode: `tailwind.config.js` set to `darkMode: 'class'` + `primary` color alias (indigo); `resources/js/app.js` registers `Alpine.store('darkMode')`; no-FOUC inline script added to `layouts/app.blade.php` and `layouts/guest.blade.php`; `x-dark-mode-toggle` component added and wired into `layouts/navigation.blade.php` (desktop + mobile).
- `static_prototype_folder/` created with a working gallery (`index.html`), a login mockup, and an admin-dashboard mockup (sticky sidebar, topbar, breadcrumbs, stat cards, chart placeholders, data table) — Tailwind CDN + Alpine CDN, zero build step, dark mode wired the same way as the real app.
- `.claude/CLAUDE.md`, `.claude/DESIGN.md`, `.claude/PLAN.md` written.
- `npm run build` verified clean. `php artisan about` verified the app boots and registers Spatie Permission.

**Not done / explicitly deferred**
- No migrations have actually been run — this sandbox has no MySQL server and no usable docker socket (permission denied, no passwordless sudo). Run `php artisan migrate --seed` on the real dev machine before doing anything else.
- No sidebar/topbar dashboard layout in the real Blade app yet — Breeze's default simple top nav is still what's live; the sticky-sidebar shell is M1.
- Chart.js not added to `package.json` yet (intentionally — see `DESIGN.md` → Architecture decisions → Frontend).
- No module (Alumni Profile, Events, Jobs, ...) has been built. `static_prototype_folder/` only has auth + admin-dashboard mockups so far.

**Environment quirks worth remembering**
- PHP 8.5 in this sandbox has no native `ext-mbstring` or `ext-curl`; mbstring only works because `phpoffice/phpspreadsheet` pulls in `symfony/polyfill-mbstring` transitively. See `CLAUDE.md` → Environment quirks.
- `composer require` needed `--ignore-platform-req=ext-mbstring` (always) and, for `maatwebsite/excel` specifically, also `--ignore-platform-req=php` (phpspreadsheet's published constraint caps at `<8.5.0`).

**Next milestone:** M1 — role-based dashboard shell (sticky sidebar, topbar, breadcrumbs, per-role route/middleware groups, empty dashboards). Prototype it in `static_prototype_folder/pages/dashboard/` first, then port to Blade.
