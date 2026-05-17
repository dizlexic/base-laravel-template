# Testing

> Read [`/AGENTS.md`](../../AGENTS.md) §6.1 first. This file is the long-form
> testing handbook for the project and expands the short rules with the
> *what*, *where*, and *how* per test pillar.

The project ships with **four testing pillars**, each with its own purpose,
runner, and home in the repo. **Every** PR that adds or changes behavior
must touch at least one of them; non-trivial features touch several.

| Pillar  | Runner               | Lives in              | Runs against                                                      |
|---------|----------------------|-----------------------|-------------------------------------------------------------------|
| Unit    | PHPUnit 12           | `tests/Unit/`         | Pure PHP, no framework boot                                       |
| Feature | PHPUnit 12 + Laravel | `tests/Feature/`      | Booted app, real DB (SQLite/MySQL), HTTP kernel                   |
| UI      | Laravel Dusk 8       | `tests/Browser/`      | Real Chromium via the `selenium` Sail service                     |
| Fuzzing | PHPUnit + Eris       | `tests/Fuzz/` *(new)* | Booted app + randomized inputs (property-based / payload fuzzing) |

All four run **inside the dev container**. Never invoke `phpunit`, `dusk`,
or `php` from the host — see [`/AGENTS.md`](../../AGENTS.md) §2.

---

## 1. Golden testing rules

1. **Bug fixes start with a failing test.** Reproduce the bug at the
   right pillar (usually Feature or UI), watch it fail, *then* fix.
2. **Features need positive + negative + edge coverage.** Happy path,
   at least one validation/authorization failure, and at least one
   obvious edge case (empty list, large payload, concurrent request,
   timezone boundary, etc.).
3. **Refactors must keep the existing suite green.** Add tests only if
   coverage of the refactored area is missing or the refactor changes a
   behavior assumption.
4. **DB tests use `RefreshDatabase`** (preferred) or
   `DatabaseTransactions`. Tests must leave no residual rows.
5. **No `@skip` / `@disabled` / commented-out tests** to make CI pass —
   fix the root cause or open a Mootasks task for the flake.
6. **Factories over seeders in tests.** Use seeders only when the feature
   under test depends on seed data (e.g. role names from `RoleSeeder`).
7. **Deterministic by default.** If a test pulls random data, seed the
   RNG so failures reproduce — see §5 on fuzzing.
8. **One assertion intent per test.** Multiple `assert*()` calls are
   fine, but each test should answer exactly one question.

---

## 2. Unit tests

**Purpose:** verify a single class / method in isolation. No HTTP, no DB,
no facades that hit IO.

- **Location:** `tests/Unit/`
- **Base class:** `PHPUnit\Framework\TestCase` (the plain one — *not*
  `Tests\TestCase`, which boots Laravel). The shipped `tests/TestCase.php`
  exists for Feature tests; Unit tests should extend the framework class
  unless they explicitly need the container.
- **Naming:** mirror the SUT path. Tests for `App\Services\PriceCalculator`
  live at `tests/Unit/Services/PriceCalculatorTest.php`. Test methods are
  `test_*` snake_case or `it_*` — match the file's existing style.
- **Mocking:** prefer hand-rolled fakes / interfaces over Mockery for
  pure-PHP collaborators. Use Mockery only when stubbing an interface is
  more work than it's worth.
- **No DB, no `RefreshDatabase`.** If a test needs the DB, it's a Feature
  test, move it.

```bash
./vendor/bin/sail artisan test --testsuite=Unit
# or a single file:
./vendor/bin/sail artisan test tests/Unit/Services/PriceCalculatorTest.php
```

Good unit-test candidates: value objects, calculators, formatters,
validators, parsers, query builders that produce SQL without executing,
event payload assemblers.

---

## 3. Feature tests

**Purpose:** verify a slice of the app end-to-end below the browser —
HTTP request → controller → DB → response, or job-dispatched → job-run →
side-effect.

- **Location:** `tests/Feature/`
- **Base class:** `Tests\TestCase` (boots Laravel via `CreatesApplication`).
- **DB:** `use RefreshDatabase;` at the top of every test class that
  touches the database. The `phpunit.xml` shipped with this project
  already sets `DB_DATABASE=testing` and `QUEUE_CONNECTION=sync`, so jobs
  run inline by default.
- **HTTP:** use Laravel's testing helpers — `$this->get(...)`,
  `$this->postJson(...)`, `$this->actingAs($user)`. Don't `Http::fake()`
  *inside* the SUT; fake outbound HTTP at the test boundary.
- **Inertia:** assert with `Inertia\Testing\AssertableInertia`
  (`$response->assertInertia(fn ($page) => $page->component('Users/Show')->has('user'))`)
  rather than rendering HTML.
- **Auth:** `actingAs(User::factory()->create())` for authenticated
  endpoints; for Sanctum APIs use
  `Sanctum::actingAs($user, ['*'])`.
- **Authorization:** every protected endpoint needs at least one test
  for the *unauthorized* case (403 / redirect) — see Spatie role rules
  in [`tech-stack.md`](tech-stack.md).
- **Mail / Notifications / Queues / Events:** use `Mail::fake()`,
  `Notification::fake()`, `Queue::fake()`, `Event::fake()` and assert
  on what was dispatched, not on side-effects.

```bash
./vendor/bin/sail artisan test --testsuite=Feature
./vendor/bin/sail artisan test --filter=PasskeyLogin
```

Good feature-test candidates: every controller action, every Form Request,
every Job's queued behavior, every Notification's `via()` channels, every
Inertia page's prop shape.

---

## 4. UI tests (Laravel Dusk)

**Purpose:** verify real-browser flows that depend on JavaScript — Inertia
page transitions, Vue component interactions, dialogs, file uploads,
client-side validation, and visual state.

- **Location:** `tests/Browser/` (already wired in this template — see
  `tests/Browser/{Pages,Components,console,screenshots,source}`).
- **Base class:** `Tests\DuskTestCase` (`tests/DuskTestCase.php`).
- **Browser:** Chromium via the **Selenium** Sail service. The
  `compose.yaml` ships a `selenium` container; Dusk's
  `driver()` in `DuskTestCase` points at it. Don't try to run Dusk
  against a host-side Chrome — it won't reach the app container.
- **DB:** `use DatabaseTruncation;` (preferred for Dusk because Dusk
  hits the *real* HTTP server and can't share a transaction with the
  test). `RefreshDatabase` is **incompatible** with Dusk and will
  silently leak data — use `DatabaseTruncation` or `DatabaseMigrations`.
- **Selectors:** prefer `@dusk` (or `data-test`) attributes on Vue
  components (`<v-btn dusk="submit-login">`). **Never** select by
  Vuetify-generated classes (`.v-btn`, `.v-text-field__input`,
  `.v-list-item--active`, …) — those are internal and change between
  Vuetify minor versions.
- **Auth shortcuts:** `$browser->loginAs($user)` skips the login flow
  for tests that aren't *about* login.
- **Artifacts:** failed runs drop screenshots into
  `tests/Browser/screenshots/` and HTML into `tests/Browser/source/`.
  Both directories are gitignored — never commit them.
- **Pages & Components:** put repeated selectors / actions into
  `tests/Browser/Pages/` (page objects) and reusable widgets into
  `tests/Browser/Components/`.

```bash
./vendor/bin/sail dusk
./vendor/bin/sail dusk --filter=LoginTest
```

Good UI-test candidates: login + 2FA happy path, role-gated admin pages
(can a non-admin even *see* the link?), Inertia form validation
round-trips, drag-and-drop, modals, file uploads, anything that requires
real Vue reactivity.

UI tests are the slowest pillar — keep the count small and meaningful.
Don't write a Dusk test for something a Feature test already covers.

---

## 5. Fuzz tests

**Purpose:** find inputs nobody thought to write a test for. Two flavors,
both live under `tests/Fuzz/`:

### 5.1 Property-based testing (preferred)

Assert *invariants* hold across a generated input space, not specific
input/output pairs.

- **Tool:** [`giorgiosironi/eris`](https://github.com/giorgiosironi/eris)
  (PHP port of QuickCheck). Add as a `require-dev` dependency.
- **Base class:** `Tests\TestCase` (so the framework is booted).
- **Trait:** `use Eris\TestTrait;` plus `RefreshDatabase` if the
  property touches the DB.
- **Seed:** Eris seeds RNG from `ERIS_SEED` env var if set; on failure
  it prints the seed + the shrunken counterexample. Always paste the
  seed into the bug report so a human can reproduce.
- **Iterations:** default 100. Bump to 500–1000 for cheap properties,
  drop to 25–50 for properties that hit the DB.

```php
use Eris\Generator;
use Eris\TestTrait;

final class SlugifyPropertyTest extends \Tests\TestCase
{
    use TestTrait;

    public function test_slug_is_idempotent(): void
    {
        $this->forAll(Generator\string())
            ->then(function (string $input) {
                $once  = slugify($input);
                $twice = slugify($once);
                $this->assertSame($once, $twice);
            });
    }
}
```

Good properties: idempotence (`f(f(x)) == f(x)`), inverse pairs
(`decode(encode(x)) == x`), commutativity, ordering preservation,
"never throws on valid input", "always rejects invalid input".

### 5.2 HTTP / Form Request fuzzing

Hammer an endpoint or a Form Request with randomized payloads to surface
500s, ungraceful validation, or auth bypasses.

- **Location:** `tests/Fuzz/Http/`
- **Approach:** generate payloads with Faker / Eris, POST them at the
  endpoint via `$this->postJson(...)`, assert the response is **always**
  one of `{200, 201, 204, 401, 403, 404, 422}` — never `500`.
- **Seed:** seed Faker explicitly (`fake()->seed(1234);`) and log the
  seed on assertion failure so the run is reproducible.
- **Iteration count:** 200 by default; cap each test at ~5s wall time
  so the suite stays fast.

```php
final class TasksApiFuzzTest extends \Tests\TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_tasks_endpoint_never_500s_under_random_input(): void
    {
        $user = \App\Models\User::factory()->create();
        $faker = fake()->seed(1234);

        for ($i = 0; $i < 200; $i++) {
            $payload = [
                'title'       => $faker->boolean(80) ? $faker->text(2000) : null,
                'description' => $faker->boolean() ? $faker->text(50_000) : null,
                'priority'    => $faker->randomElement([null, 'low', 'med', 'critical', 'lol', 42]),
                'due_at'      => $faker->boolean(70) ? $faker->dateTime->format('c') : $faker->word(),
            ];

            $response = $this->actingAs($user)->postJson('/api/tasks', $payload);

            $this->assertContains(
                $response->status(),
                [200, 201, 204, 401, 403, 404, 422],
                "5xx on payload: ".json_encode($payload),
            );
        }
    }
}
```

Good fuzz targets: every JSON API endpoint that accepts user input,
search/filter query strings, file-upload endpoints (size + MIME), and
anything that parses a structured value (UUID, ULID, date, JSON column).

### 5.3 Running fuzz tests

Fuzz tests live in their own PHPUnit suite so CI can run them on a
schedule (nightly) instead of on every PR:

```xml
<!-- phpunit.xml -->
<testsuite name="Fuzz">
    <directory>tests/Fuzz</directory>
</testsuite>
```

```bash
./vendor/bin/sail artisan test --testsuite=Fuzz
ERIS_SEED=1735 ./vendor/bin/sail artisan test --testsuite=Fuzz   # reproduce a failure
```

`sail composer ci:check` runs **Unit + Feature** by default. Fuzz is
opt-in — agents should run it locally for any PR that changes an
exposed endpoint or a parser, and CI runs it nightly against `main`.

---

## 6. Where each pillar fits

A typical "add a feature" PR exercises:

1. **Unit** — pure logic the feature introduces (calculators, validators,
   value objects).
2. **Feature** — every new HTTP route, Form Request, Policy, Notification,
   and Job, both happy and unauthorized paths.
3. **UI** — *one* Dusk test for the user-visible flow, only if the
   feature has a visible UI surface.
4. **Fuzz** — *one* fuzz test per new public endpoint, at minimum the
   "never 500" property.

A "fix a bug" PR usually only adds 1–2 of the above (the pillar that
reproduces the bug), but **must** include the failing test.

---

## 7. Quick command reference

```bash
# All Unit + Feature (the CI default)
./vendor/bin/sail artisan test

# Just one pillar
./vendor/bin/sail artisan test --testsuite=Unit
./vendor/bin/sail artisan test --testsuite=Feature
./vendor/bin/sail artisan test --testsuite=Fuzz

# Single file / single method
./vendor/bin/sail artisan test tests/Feature/Auth/LoginTest.php
./vendor/bin/sail artisan test --filter=test_user_can_login

# Browser tests (Selenium)
./vendor/bin/sail dusk
./vendor/bin/sail dusk --filter=LoginTest

# Full CI bundle (pint + lint + types + Unit/Feature)
./vendor/bin/sail composer ci:check
```

When in doubt, copy the closest existing test, change the assertions,
and run it. The fastest way to learn the project's testing dialect is
to read `tests/Feature/Auth/` and `tests/Browser/`.
