# Tech stack & per-package conventions

> Read [`/AGENTS.md`](../../AGENTS.md) first. This file expands §3 with the
> *why* and the *house rules* per package.

## Runtime & framework

- **PHP 8.3+** — match the version in `composer.json` `"require"`. The Sail
  runtime is built from `vendor/laravel/sail/runtimes/8.5`, so the container
  ships PHP 8.5 — write code compatible with both.
- **Laravel 13** — use the framework-provided abstractions (Form Requests,
  Resources, Policies, Notifications, Jobs, Mailables, Events) rather than
  rolling your own. New global middleware is registered in
  `bootstrap/app.php` (Laravel 13 style), not `app/Http/Kernel.php`.

## Frontend

- **Inertia.js + Vue 3 + TypeScript + Vite**. Pages live in
  `resources/js/Pages/...`, layouts in `resources/js/Layouts/...`, shared
  components in `resources/js/Components/...`. Component filenames are
  PascalCase (`UserCard.vue`).
- Use `<script setup lang="ts">` for new components.
- Type-check with `npm run types:check` (runs `vue-tsc`); ESLint and
  Prettier handle style. Configs are in `eslint.config.js` and the project
  root.
- Routes are exposed to the frontend via **Wayfinder** — prefer
  `route('users.show', user)` helpers over hard-coded URL strings.

## Authentication & authorization

- **Fortify** powers the auth screens (login, register, password reset,
  email verification, two-factor). Customize via `config/fortify.php` and
  `App\Providers\FortifyServiceProvider`.
- **Sanctum** issues API tokens for first-party clients (the
  `personal_access_tokens` table is already migrated). SPAs continue to use
  cookie-based sessions.
- **Socialite** handles OAuth providers; add provider config to
  `config/services.php` and never to `.env.example` with real client ids.
- **Spatie Laravel Permission** is the *only* role/permission system in this
  project. Conventions:
  - Roles are seeded in `database/seeders/RoleSeeder.php` so a fresh
    `php artisan migrate:fresh --seed` produces a usable system.
  - Permission *names* are dot-namespaced: `users.create`, `tasks.delete`.
  - Roles are slug-cased: `admin`, `editor`, `viewer`.
  - Authorization happens through Gates / Policies, not by string-checking
    role names in controllers. (`$user->can('users.create')` is fine;
    `$user->hasRole('admin')` in business logic is not.)

## Search

- **Laravel Scout** is wired up; the default driver is whatever
  `SCOUT_DRIVER` is set to in `.env`. Use `database` for local development,
  swap to a real driver (Meilisearch, Algolia, Typesense) per environment.
- Searchable models use the `Searchable` trait and override
  `toSearchableArray()` to *only* expose fields safe for an external index.

## Queues & workers

- **Redis** is the queue backend (`QUEUE_CONNECTION=redis`).
- **Laravel Horizon** is the supervisor + dashboard. Config lives in
  `config/horizon.php`; production tuning happens via the `production`
  environment in that file.
- The Horizon dashboard is gated by `Gate::define('viewHorizon', ...)` in
  `App\Providers\HorizonServiceProvider`. Restrict it to admins in prod.
- In Docker, Horizon runs as a **systemd unit** (`horizon.service`) inside
  the app container — systemd is PID 1, the unit auto-restarts on crash,
  and `horizon:terminate` is bound to `ExecStop` so deploys finish their
  in-flight jobs. See [`docker.md`](docker.md) §4.

## Observability

- **Laravel Telescope** is for inspecting requests, queries, jobs, mail,
  notifications, and exceptions during development.
  - In `production`, Telescope is restricted by
    `Gate::define('viewTelescope', ...)` in
    `App\Providers\TelescopeServiceProvider`.
  - Recording is gated by `Telescope::filter(...)` so we don't fill prod
    storage; only exceptions and slow queries are recorded by default.
- **Discord error notifications**: use the
  [`laravel-notification-channels/discord`](https://github.com/laravel-notification-channels/discord)
  package. Wire it up by:
  1. Sending a `Notification` from the global exception handler whenever
     an exception is reported.
  2. Routing the notification via `DiscordChannel` to the
     `DISCORD_WEBHOOK_URL` env value.
  3. Truncating exception messages and including a link to Telescope (in
     environments where Telescope is reachable).

## Code style

- **Laravel Pint** with the `laravel` preset (`pint.json`). Run via
  `sail composer lint` (auto-fix) or `sail composer lint:check` (verify).
- **ESLint + Prettier** for JS/TS/Vue; configs are project-root.
- Use `declare(strict_types=1);` only where the surrounding files already
  do — match the established pattern, don't selectively introduce it.

## Testing

The project ships with **four testing pillars**. Full conventions,
examples, and commands live in [`testing.md`](testing.md); per-package
notes:

- **PHPUnit 12** is the test runner for **Unit** (`tests/Unit/`),
  **Feature** (`tests/Feature/`), and **Fuzz** (`tests/Fuzz/`) suites.
  `phpunit.xml` configures the named test suites.
  - **Unit** tests extend `PHPUnit\Framework\TestCase` (no framework
    boot, no DB, no facades that touch IO).
  - **Feature** tests extend `Tests\TestCase` and use
    `RefreshDatabase` (or `DatabaseTransactions`) on every class that
    touches the DB.
  - **Fuzz** tests are an opt-in suite — property-based assertions
    (via [`giorgiosironi/eris`](https://github.com/giorgiosironi/eris))
    plus randomized HTTP payload tests that assert public endpoints
    never return `5xx`. Always seed the RNG (`ERIS_SEED=…` or
    `fake()->seed(…)`) so failures reproduce.
- **Laravel Dusk 8** drives **UI** tests in `tests/Browser/` against the
  `selenium` Sail service (`./vendor/bin/sail dusk`). Dusk is
  **incompatible with `RefreshDatabase`** — use `DatabaseTruncation`
  (or `DatabaseMigrations`). Prefer `@dusk` attribute selectors over
  Tailwind class names.
- **Factories** live in `database/factories/`, **seeders** in
  `database/seeders/`. Tests should use factories, not seeders, except
  when a feature explicitly depends on seed data (e.g. role names from
  `RoleSeeder`).
- Coverage isn't enforced numerically, but every PR that adds logic
  must add tests at the right pillar — see
  [`testing.md`](testing.md) §6 for which pillar fits which change.

## Identifiers (UUIDs)

- API-exposed resources use UUIDs as their *external* identifier. Either:
  - Make the UUID the primary key via `HasUuids`, *or*
  - Keep the integer PK and add a unique `uuid` column, then override
    `getRouteKeyName()` to return `'uuid'`.
- Pivot, queue, cache, session, and similar internal tables stay on
  auto-increment integer PKs, but morph / FK columns that point at an
  API-exposed model must be `uuid` / `uuidMorphs` so the types match
  (e.g. `personal_access_tokens.tokenable` is `uuidMorphs` because
  Sanctum tokens polymorph to the UUID `User`).
- Already wired in this template:
  - `App\Models\User` uses `HasUuids`; `users.id` is a UUID.
  - `passkeys` and `sessions` use `foreignUuid` for `user_id`.
  - The published Spatie Permission migration
    (`database/migrations/…_create_permission_tables.php`) uses UUID PKs
    on `roles` / `permissions` and a UUID `model_id` morph.
  - `App\Models\Role` and `App\Models\Permission` extend the Spatie
    models and mix in `HasUuids`; `config/permission.php` points at
    these app-level classes.

## Mail

- **Mailpit** captures all outbound mail in local dev. Set
  `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025` in `.env`.
- All mail is sent through `Mailable` classes, never inline `Mail::raw(...)`.
