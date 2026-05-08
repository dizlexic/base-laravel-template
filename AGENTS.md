# AGENTS.md

> **Cornerstone document for AI / autonomous agents working in this repository.**
> Humans: this file is also the canonical engineering handbook for the project.
> Read this before touching code. If something here conflicts with another doc,
> *this file wins* — open a PR to update it rather than diverging.

This file is intentionally **copy-paste-portable** across our Laravel projects.
Anything project-specific is either fenced as a placeholder (e.g. `<board-id>`)
or pulled from environment variables.

---

## 1. Project at a glance

This is a **Laravel 13** application using the **Inertia + Vue 3** starter kit.
It is developed locally via **Laravel Sail** (Docker), uses **MySQL 8.4** and
**Redis**, and ships with **Horizon**, **Telescope**, **Fortify**, **Sanctum**,
**Scout**, **Socialite**, **Wayfinder**, and **Spatie Permission** preinstalled.

Detailed breakdown lives in [`docs/agents/tech-stack.md`](docs/agents/tech-stack.md).

---

## 2. Golden rules for agents

1. **Run everything inside the dev container.** Never invoke `php`, `composer`,
   `npm`, `pnpm`, `node`, `artisan`, or `phpunit` on the host. Use one of:
   - `./vendor/bin/sail <cmd>` (preferred)
   - `docker compose exec laravel.test <cmd>`
   - `docker exec -it <container> <cmd>`
2. **Pint is law.** Every change must pass `sail composer lint:check`.
   When in doubt, run `sail composer lint` to auto-format.
3. **Tests must pass.** `sail artisan test` must be green before you submit
   for review. Never `@skip`, `@disabled`, or comment out a failing test to
   make CI green — fix the root cause.
4. **Use the Mootasks board.** If a Mootasks MCP server is connected, claim a
   task with `accept-task` *before* writing code, and `submit-for-review` when
   done. See [`docs/agents/mootasks.md`](docs/agents/mootasks.md).
5. **Branch + commit format is mandatory** when a task id exists. See §5.
6. **Don't escalate scope.** Stay within the task you accepted; file new
   Mootasks tasks for follow-ups instead of bundling them into the same PR.
7. **Never commit secrets.** `.env` is gitignored; new keys go in
   `.env.example` with a safe default or empty value.

---

## 3. Tech stack (short form)

| Layer            | Choice                                                  |
|------------------|---------------------------------------------------------|
| PHP              | 8.3+                                                    |
| Framework        | Laravel 13                                              |
| Frontend         | Inertia.js + Vue 3 + TypeScript + Vite                  |
| Local dev        | Laravel Sail (Docker Compose)                           |
| Database         | MySQL 8.4 (with phpMyAdmin in Docker)                   |
| Cache / Queue    | Redis + Laravel Horizon                                 |
| Auth             | Fortify + Sanctum (+ Socialite for OAuth)               |
| Search           | Laravel Scout                                           |
| Authorization    | Spatie Laravel Permission (roles + permissions)         |
| Observability    | Laravel Telescope (errors mirrored to Discord)          |
| Code style       | Laravel Pint (`laravel` preset)                         |
| Tests            | PHPUnit 12 + Laravel Dusk (browser)                     |
| Mail (local)     | Mailpit                                                 |
| Browser tests    | Selenium (Chromium) via Sail                            |

Full per-package guidance: [`docs/agents/tech-stack.md`](docs/agents/tech-stack.md).

---

## 4. Local development

```bash
# First-time setup
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Service URLs (defaults):

| Service         | URL                          |
|-----------------|------------------------------|
| App             | http://localhost             |
| Vite dev server | http://localhost:5173        |
| Mailpit UI      | http://localhost:8025        |
| phpMyAdmin      | http://localhost:8080        |
| Horizon         | http://localhost/horizon     |
| Telescope       | http://localhost/telescope   |

Docker / production container details (Horizon as a systemd-managed service,
publishing the Sail Dockerfile, etc.) live in
[`docs/agents/docker.md`](docs/agents/docker.md).

---

## 5. Git workflow

We use **trunk-based development** with short-lived feature branches off `main`,
**squash-merged** via pull request.

### 5.1 Branch naming

Whenever a Mootasks task id exists, the branch **must** include it:

```
feature/<task-id>_<short-slug>
```

Examples:

```
feature/MOO-123_add-login
feature/MOO-456_fix-uuid-on-tasks-api
```

For exceptional non-task work (hotfixes, infra), prefix with `hotfix/` or
`chore/` and omit the task id only if no task exists:

```
hotfix/<short-slug>
chore/<short-slug>
```

### 5.2 Commit messages

Every commit subject **must** start with the Mootasks task id in brackets,
followed by a Conventional-Commits-style type:

```
[MOO-123] feat: add passkey login flow
[MOO-123] test: cover passkey edge cases
[MOO-456] fix: use uuid on /api/tasks responses
```

Commits without a task id are only allowed for `chore:` / `hotfix:` work that
has no Mootasks ticket.

### 5.3 Pull requests

- Open the PR against `main`.
- PR title mirrors the squash-merge commit, e.g. `[MOO-123] feat: add passkey login flow`.
- PR description must:
  - Link the Mootasks task (paste the task URL or id).
  - Summarize the change in 1–3 bullet points.
  - List anything reviewers should manually verify.
- CI must be green: Pint, PHPUnit, and the JS toolchain (`lint:check`,
  `format:check`, `types:check`) — all of these are wrapped by
  `sail composer ci:check`.
- **Squash-merge only.** No merge commits, no rebase-merge.

### 5.4 Co-authoring

When an agent writes the code, add a `Co-authored-by:` trailer so credit is
visible in `git log`:

```
Co-authored-by: Junie <junie@jetbrains.com>
```

---

## 6. Code style & quality gates

Before you mark a Mootasks task `submit-for-review`, **all** of the following
must pass inside the container:

```bash
./vendor/bin/sail composer ci:check
```

That command runs, in order:

1. `pint --parallel --test`             — PHP formatting
2. `npm run lint:check`                 — ESLint
3. `npm run format:check`               — Prettier
4. `npm run types:check`                — `vue-tsc`
5. `php artisan test`                   — PHPUnit suite

Quick auto-fix loops while developing:

```bash
./vendor/bin/sail composer lint        # pint --parallel  (auto-format PHP)
./vendor/bin/sail npm run lint         # ESLint --fix
./vendor/bin/sail npm run format       # Prettier --write
```

### 6.1 Testing requirements

The project ships with **four testing pillars**. The full handbook is in
[`docs/agents/testing.md`](docs/agents/testing.md); the short rules:

 Pillar   | Runner               | Lives in        | Use for                                                   |
----------|----------------------|-----------------|-----------------------------------------------------------|
 Unit     | PHPUnit 12           | `tests/Unit/`   | Pure-PHP classes (calculators, value objects, parsers)    |
 Feature  | PHPUnit 12 + Laravel | `tests/Feature/`| HTTP routes, Form Requests, Policies, Jobs, Notifications |
 UI       | Laravel Dusk 8       | `tests/Browser/`| Real-browser flows that depend on Vue / JavaScript        |
 Fuzz     | PHPUnit + Eris       | `tests/Fuzz/`   | Property-based + randomized-payload tests on parsers/APIs |

Per change type:

- **Bugs**: write a failing test (at the right pillar) that reproduces
  the bug *before* fixing it.
- **Features**: cover the happy path **(Feature)**, at least one negative
  path — validation / authorization — **(Feature)**, the user-visible
  flow if there is one **(UI / Dusk)**, the pure-logic units the feature
  introduces **(Unit)**, and at least the "never 500s" property on any new
  public endpoint **(Fuzz)**.
- **Refactors**: existing tests must still pass. Add tests if coverage of
  the refactored area is missing.

Cross-cutting rules:

- Use `RefreshDatabase` (or `DatabaseTransactions`) in *every* PHPUnit test
  that touches the database. Tests must leave no residual data.
- **Dusk is incompatible with `RefreshDatabase`** — use
  `DatabaseTruncation` (or `DatabaseMigrations`) instead.
- Browser tests run against the `selenium` Sail service via
  `./vendor/bin/sail dusk`. Use `@dusk` attributes as selectors, not
  Tailwind classes.
- Fuzz tests live in their own PHPUnit suite and are **opt-in** — agents
  run them locally for any PR that changes a parser or a public endpoint;
  CI runs the suite nightly. Always seed the RNG (`ERIS_SEED=…` or
  `fake()->seed(…)`) so failures reproduce.
- Never `@skip`, `@disabled`, comment out, or weaken a failing test to
  make CI green — fix the root cause.

### 6.2 Authorization

Use **Spatie Laravel Permission** for roles and permissions. Do not invent a
parallel role system. Roles live in seeders; per-action permissions are
checked via Gates / Policies / `$user->can(...)` middleware.

### 6.3 Identifiers

- **API-exposed resources** (anything reachable via a public/auth'd HTTP route
  whose id appears in a URL or response body) must use **UUIDs** as their
  external identifier. Use Laravel's `HasUuids` trait, or expose a separate
  `uuid` column as the route key via `getRouteKeyName()`.
- **Internal-only tables** (pivot tables, queue tables, sessions, cache, etc.)
  may keep auto-increment integer primary keys. Don't UUID them just for the
  sake of consistency. The morph / FK columns that *point* at API-exposed
  models must still be `uuid` / `uuidMorphs` so the types match.
- The base template already follows this rule:
  - `users.id` is a UUID (`HasUuids` on `App\Models\User`).
  - `passkeys` and `sessions` use UUID FKs to `users`.
  - `personal_access_tokens.tokenable` uses `uuidMorphs` (Sanctum tokens
    polymorph to the UUID `User`).
  - The published Spatie Permission migration
    (`database/migrations/…_create_permission_tables.php`) uses UUID PKs
    on `roles` / `permissions` and a UUID `model_id` morph; the matching
    `App\Models\Role` and `App\Models\Permission` extend the Spatie
    models and mix in `HasUuids`. `config/permission.php` points at the
    app-level classes.

---

## 7. Mootasks (taskboard MCP server)

Mootasks is the source of truth for what an agent should be working on.

- The MCP connection lives in **`.junie/mcp/mcp.json`** (gitignored — every
  developer / agent populates their own after cloning). The shipped file
  references both the board id and the bearer token from environment
  variables — `${MOOTASKS_BOARD_ID}` and `${MOOTASKS_TOKEN}` — so the same
  config is portable across projects.
- Both env vars live in the project's gitignored **`.env`** file
  (`.env.example` ships empty placeholders so they're discoverable on a
  fresh clone). Fill them in from your board's **Show MCP Config** drawer.
- Full setup, the env-var pattern, the tool list, and security notes are in
  [`docs/agents/mootasks.md`](docs/agents/mootasks.md).
- One agent identity, one task at a time. Always `accept-task` *before*
  writing code; always `submit-for-review` when finished. Never move a task
  straight to `done`.

---

## 8. Observability

- **Telescope** is the local-first debugger; it's enabled in `local` and
  guarded by `Gate::define('viewTelescope', ...)` in production.
- **Errors are mirrored to Discord** via the
  `laravel-notification-channels/discord` package. The webhook URL is
  configured via `DISCORD_WEBHOOK_URL` in `.env`. Agents should never
  silence or remove this notification path.
- **Horizon** is the queue dashboard. In Docker, `php artisan horizon` runs
  as a **systemd unit** (`horizon.service`) inside the app container —
  systemd is PID 1, so the unit auto-restarts on crash, runs
  `horizon:terminate` on shutdown, and gets a graceful 1-hour stop window
  for in-flight jobs. See [`docs/agents/docker.md`](docs/agents/docker.md) §4.

---

## 9. Workflow checklist (TL;DR for an agent picking up work)

A condensed version of the per-task loop. The full version is in
[`docs/agents/workflow.md`](docs/agents/workflow.md).

1. **Read** `moo-tasks://<board-id>/agent-instructions` and the
   `task-workflow` prompt.
2. `list-tasks` → pick highest-priority `todo` task → `get-task` (and
   `get-comments`) for full context.
3. `accept-task` with your stable agent name (e.g. `"junie"`).
4. **Branch**: `git checkout -b feature/<task-id>_<short-slug>` off latest
   `main`.
5. Implement the change inside the container. Add/adjust tests.
6. `sail composer ci:check` — must be green.
7. Commit: `[MOO-123] feat: …` (with `Co-authored-by:` trailer if relevant).
8. Push, open PR against `main`, link the Mootasks task in the description.
9. `submit-for-review` on the Mootasks board.
10. If a reviewer files a *correction task* linked to yours, treat it as a
    new top-priority item and repeat from step 2.

---

## 10. Where to look next

- [`docs/agents/tech-stack.md`](docs/agents/tech-stack.md) — every package and
  why it's installed; conventions per package.
- [`docs/agents/workflow.md`](docs/agents/workflow.md) — full per-task workflow,
  including failure / clarification handling.
- [`docs/agents/testing.md`](docs/agents/testing.md) — the four testing pillars
  (Unit, Feature, UI/Dusk, Fuzz) with conventions, examples, and commands.
- [`docs/agents/mootasks.md`](docs/agents/mootasks.md) — Mootasks MCP setup,
  available tools, and the `MOOTASKS_BOARD_ID` / `MOOTASKS_TOKEN` env-var pattern.
- [`docs/agents/docker.md`](docs/agents/docker.md) — Docker / Sail
  conventions, phpMyAdmin, and the production Horizon supervisor setup.
