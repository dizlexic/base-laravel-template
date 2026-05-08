# Agent workflow

> Read [`/AGENTS.md`](../../AGENTS.md) first. This is the long-form version of
> §9 in that file — when there's a Mootasks board attached, this is *the*
> per-task loop. When there isn't, skip the Mootasks-specific steps and use
> the section called "Non-Mootasks workflow" at the bottom.

---

## 1. Before you start

1. Pull the latest `main`:
   ```bash
   git checkout main
   git pull --ff-only origin main
   ```
2. Bring up the dev container if it isn't already running:
   ```bash
   ./vendor/bin/sail up -d
   ```
3. Sanity-check dependencies:
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ```

## 2. Discover work

Read the board's own instructions first — they may override this document:

- `moo-tasks://<board-id>/agent-instructions`
- the `task-workflow` MCP prompt

Then list available work:

- `list-tasks` filtered by `status=todo`, sorted by priority.
- Pick the highest-priority unclaimed task. Tie-breakers, in order:
  `critical` > `high` > `medium` > `low`, then oldest first.
- `get-task <id>` for the full description.
- `get-comments <id>` to read prior context.

If the task is unclear, **don't accept it.** Post a question via
`add-comment` and pick a different task.

## 3. Accept the task

```
accept-task(taskId, agentName)
```

- `agentName` is your stable handle. Use the same one every run, e.g.
  `"junie"`, `"claude-code"`. This is how humans see who owns what.
- One agent identity, one in-progress task. Never accept a second task
  while you have one in progress.

## 4. Branch

```bash
git checkout -b feature/<task-id>_<short-slug>
```

- `<task-id>` is exactly the Mootasks task id (e.g. `MOO-123`).
- `<short-slug>` is 2–5 lowercase words separated by `-`, summarising
  the task. Don't paste the whole task title.

Examples:

```
feature/MOO-123_add-login
feature/MOO-456_fix-uuid-on-tasks-api
feature/MOO-789_horizon-supervisor-config
```

For non-task work (rare):

```
hotfix/<short-slug>
chore/<short-slug>
```

## 5. Implement

- Run every command inside the container (`./vendor/bin/sail …`,
  `docker compose exec laravel.test …`).
- Follow the package conventions in [`tech-stack.md`](tech-stack.md).
- Stay inside the scope of the task. If you discover a separate issue,
  file a new Mootasks task with `create-task` instead of fixing it
  inline.
- Communicate non-trivial decisions or blockers via `add-comment` on
  the task — comments are visible to humans in real time.

## 6. Test

The project has **four testing pillars** — Unit, Feature, UI (Dusk),
and Fuzz. Full handbook: [`testing.md`](testing.md).

- **Bug fix?** Write a failing test first at the right pillar (usually
  Feature or UI), watch it fail, then fix.
- **Feature?** Cover the happy path + at least one negative path
  (validation / authorization) + obvious edge cases. New public
  endpoint → also add a fuzz test that asserts "never 500s". Visible
  UI change → add one Dusk test for the user-facing flow.
- **Refactor?** Run the existing suite first, refactor, then re-run.
- Always use `RefreshDatabase` (or `DatabaseTransactions`) in PHPUnit
  DB tests. Dusk tests use `DatabaseTruncation` instead —
  `RefreshDatabase` is incompatible with Dusk.
- Seed the RNG on any randomized test (`ERIS_SEED=…` or
  `fake()->seed(…)`) so failures reproduce.

```bash
./vendor/bin/sail artisan test                       # Unit + Feature (CI default)
./vendor/bin/sail artisan test --testsuite=Unit
./vendor/bin/sail artisan test --testsuite=Feature
./vendor/bin/sail artisan test --testsuite=Fuzz      # opt-in, run if you touched a parser/endpoint
./vendor/bin/sail dusk                               # only if browser tests changed
```

## 7. Quality gate

Run the full CI bundle inside the container:

```bash
./vendor/bin/sail composer ci:check
```

Equivalent to:

1. `pint --parallel --test`
2. `npm run lint:check`
3. `npm run format:check`
4. `npm run types:check`
5. `php artisan test`

Fix everything until this returns 0. Do **not** disable checks to make it
pass.

Auto-fix loops while iterating:

```bash
./vendor/bin/sail composer lint
./vendor/bin/sail npm run lint
./vendor/bin/sail npm run format
```

## 8. Commit

Subject line *must* start with `[<task-id>]` and a Conventional Commits
type. Keep the subject ≤ 72 chars. Body wraps at 72.

```
[MOO-123] feat: add passkey login flow

- Adds Passkey controller + routes
- Adds RefreshDatabase test covering happy + invalid path
- Updates AGENTS.md §6.3 (none needed here)

Refs: MOO-123

Co-authored-by: Junie <junie@jetbrains.com>
```

Use the trailer flag on the CLI to avoid hand-editing:

```bash
git commit -m "[MOO-123] feat: add passkey login flow" \
  --trailer "Refs: MOO-123" \
  --trailer "Co-authored-by: Junie <junie@jetbrains.com>"
```

## 9. Open a pull request

- Push the branch: `git push -u origin feature/MOO-123_add-login`.
- Open the PR against `main`.
- PR title = squash commit subject (e.g. `[MOO-123] feat: add passkey login flow`).
- PR description must contain:
  - Mootasks task id + link.
  - 1–3 bullet summary of the change.
  - Anything reviewers should manually verify (URLs, fixtures, data).
- CI must be green.
- **Squash-merge only.**

## 10. Submit for review on the board

```
submit-for-review(taskId)
```

Do **not** move the task to `done` yourself — humans (or a reviewer agent)
do that after PR merge.

## 11. Handle review feedback

If a reviewer creates a *correction task* linked to your original (parent)
task:

1. Treat it as a new top-priority item.
2. `accept-task` on the correction.
3. Open a *new* branch off the latest `main` (after your original PR
   merged) — do not push to a merged branch:
   ```
   feature/<correction-task-id>_<short-slug>
   ```
4. Address the feedback, re-run §7, and `submit-for-review`.

---

## Failure modes & escalation

- **Task is ambiguous** → `add-comment` asking for specifics, leave it in
  `todo`, pick a different task.
- **Blocked on infra/credentials** → `add-comment` describing the blocker,
  *don't* hack around it. Don't put real secrets into the comment.
- **Tests fail intermittently** → assume it's your change first. Re-run
  the suite. If you confirm flakiness predates your change, open a new
  Mootasks task for the flake instead of disabling the test.
- **Pint disagrees with you** → Pint wins. Run `sail composer lint`.
- **You can't get CI green in a reasonable number of attempts** →
  `add-comment` with the failing output and wait for human guidance
  rather than weakening tests / config.

---

## Non-Mootasks workflow

If no Mootasks task exists (hotfix, infra spike, agent dogfooding), the
rules are the same except:

- Branch name: `hotfix/<short-slug>` or `chore/<short-slug>`.
- Commit subject: drop the `[MOO-…]` prefix, keep the Conventional Commits
  type: `chore: bump pint to 1.28`.
- PR description must explain *why* there's no task and link any
  supporting issue/Slack thread.

Whenever practical, **create a Mootasks task retroactively** with
`create-task` so the work is tracked.
