# Mootasks (taskboard MCP server)

> Read [`/AGENTS.md`](../../AGENTS.md) and [`workflow.md`](workflow.md) first.
> This file documents how to **connect** to the Mootasks board and which MCP
> tools are available. The per-task workflow (accept → branch → PR →
> submit-for-review) is in `workflow.md`.

---

## 1. What is Mootasks?

Mootasks is a kanban board that exposes each board as its own
[Model Context Protocol](https://modelcontextprotocol.io/specification/2025-11-25)
(MCP) server. The endpoint is **scoped to a single board** — an agent
connected to one board can never see or modify tasks on any other board.

- **Endpoint URL:** `https://mootasks.dev/api/boards/<board-id>/mcp`
- **Transport:** `streamable-http`
- **Auth:** `Authorization: Bearer <token>` (per
  [MCP basic spec](https://modelcontextprotocol.io/specification/2025-11-25/basic))

> ⚠️ Tokens via `?token=` query string are **not supported.** Always use the
> `Authorization` header.

---

## 2. The shipped config: `.junie/mcp/mcp.json`

This project ships its Mootasks connection details at:

```
.junie/mcp/mcp.json
```

That path is **gitignored** (see the project root `.gitignore`, which
ignores all of `/.junie`), so it's a per-developer / per-agent file —
nothing you put in it leaves your machine via git.

Even so, **both** the board id and the bearer token are referenced from
environment variables rather than hardcoded into the file. That keeps the
config portable: the same `.junie/mcp/mcp.json` works across every
Mootasks-backed project you clone — only the env vars change.

The shipped shape is:

```json
{
    "mcpServers": {
        "moo-tasks": {
            "url": "https://mootasks.dev/api/boards/${MOOTASKS_BOARD_ID}/mcp",
            "headers": {
                "Authorization": "Bearer ${MOOTASKS_TOKEN}"
            }
        }
    }
}
```

### 2.1 Required environment variables

Both values live in the project's **`.env`** file (which is gitignored,
so the token never leaves your machine via git). `.env.example` ships
empty placeholders so the keys are discoverable on a fresh clone:

```dotenv
# Mootasks (taskboard MCP server) — see docs/agents/mootasks.md
MOOTASKS_BOARD_ID=
MOOTASKS_TOKEN=
```

| Variable            | Purpose                                                                                  |
|---------------------|------------------------------------------------------------------------------------------|
| `MOOTASKS_BOARD_ID` | Board id this project is tracked on (from the board's URL / **Show MCP Config** drawer). |
| `MOOTASKS_TOKEN`    | Bearer token issued by the Mootasks board settings drawer.                               |

Laravel itself reads `.env` automatically. For the MCP client, however,
these placeholders need to be visible in the **process environment** at
the moment the client loads `.junie/mcp/mcp.json`. The simplest options:

- **Sourced from `.env` by your shell / IDE.** PhpStorm + Junie will pick
  up project-level env vars when the IDE is launched from a shell that
  already exported them. A small zsh / bash helper that runs on `cd`
  into the project (e.g. via [direnv](https://direnv.net)) is the most
  common setup:

  ```bash
  # .envrc (direnv)
  set -a
  source .env
  set +a
  ```

- **Exported manually in your shell profile** (`~/.zshrc`,
  `~/.config/fish/config.fish`, …) — handy if you only work on a single
  Mootasks-backed project at a time:

  ```bash
  export MOOTASKS_BOARD_ID="<paste from .env>"
  export MOOTASKS_TOKEN="<paste from .env>"
  ```

Either way, MCP clients that perform `${VAR}` substitution will resolve
the placeholders in `.junie/mcp/mcp.json` at load time.

### 2.2 Per-project setup

When you fork this template into a new project:

1. Create a new Mootasks board for that project at
   [https://mootasks.dev](https://mootasks.dev).
2. Open the board's **Show MCP Config → 📋 Copy JSON** drawer to grab
   the new board id and a fresh bearer token.
3. Set `MOOTASKS_BOARD_ID` and `MOOTASKS_TOKEN` in the new project's
   `.env`. The shipped `.junie/mcp/mcp.json` does **not** need to change
   per project.

> Because `.junie/` is gitignored, every developer / agent on the team
> still needs to populate their own `.junie/mcp/mcp.json` after cloning
> (typically by copying the shape above).

---

## 3. Wiring the same config into other MCP clients

The JetBrains / Junie path is the file at `.junie/mcp/mcp.json` above. If
you also drive this project from another agent, drop the **same JSON
shape** into that client's config:

| Client       | Path                                     |
|--------------|------------------------------------------|
| Junie        | `.junie/mcp/mcp.json` (shipped)          |
| JetBrains    | Settings → Tools → MCP Servers           |
| Claude Code  | `~/.claude.json` or project `.mcp.json`  |
| Cursor       | `.cursor/mcp.json`                       |
| VS Code      | `.vscode/mcp.json`                       |

Most clients also accept a `"type": "streamable-http"` field; Junie
auto-detects the transport from the URL, so it's omitted in the shipped
file but is fine to add for other clients:

```json
{
  "mcpServers": {
    "moo-tasks": {
      "type": "streamable-http",
      "url": "https://mootasks.dev/api/boards/${MOOTASKS_BOARD_ID}/mcp",
      "headers": {
        "Authorization": "Bearer ${MOOTASKS_TOKEN}"
      }
    }
  }
}
```

If the target client doesn't perform env-var substitution in its MCP
config, inline the literal values instead — but keep the source of truth
(`MOOTASKS_BOARD_ID` / `MOOTASKS_TOKEN`) documented in your shell so
other tools can pick them up.

---

## 4. Available MCP tools

Subject to per-board toggles in board settings:

| Tool                  | Purpose                                                |
|-----------------------|--------------------------------------------------------|
| `list-tasks`          | Discover open tasks (filter by status / priority)      |
| `get-task`            | Read full details of a single task                     |
| `get-comments`        | Read all comments on a task                            |
| `accept-task`         | Claim a task — assigns you and sets `in_progress`      |
| `add-comment`         | Post progress notes / questions on a task              |
| `update-task-status`  | Move a task between columns                            |
| `submit-for-review`   | Mark a task ready for human review                     |
| `create-task`         | File a new task on the board                           |
| `delete-task`         | Remove a task (use sparingly)                          |

Plus resources / prompts (the `<board-id>` segment is the value of
`MOOTASKS_BOARD_ID` from `.env`):

- `moo-tasks://<board-id>/board-state` — full board snapshot
- `moo-tasks://<board-id>/agent-instructions` — board-specific guidance
  that may **override** anything in this repo
- `task-workflow` — guided workflow prompt

Always read `agent-instructions` and the `task-workflow` prompt **before**
accepting a task — they're the most up-to-date source of truth.

---

## 5. Hard rules

- ✅ Always `accept-task` **before** writing code, so humans see who's
  working.
- ✅ One task at a time per agent identity.
- ✅ If a task is unclear, leave a comment asking for clarification rather
  than guessing — and leave the task in `todo` (don't accept it yet).
- ❌ Don't `delete-task` unless explicitly told to.
- ❌ Don't move tasks straight to `done` — always go through `review` via
  `submit-for-review`.
- ❌ Don't try to access tasks from other boards; this token only works
  for the single board it was issued for.

---

## 6. Security notes for humans

- Treat the bearer token like a password. Anyone with it can act as an
  agent on the board.
- `.junie/mcp/mcp.json` is gitignored, so neither the env-var
  placeholders nor any inlined value will be leaked via git — but if you
  ever inline real values, they're still on disk in plain text. Don't
  sync `.junie/` into cloud-backed dotfile repos without thinking about
  it.
- `.env` itself is gitignored in this template, so it's safe to put
  `MOOTASKS_BOARD_ID` and `MOOTASKS_TOKEN` there. Never copy those values
  into `.env.example`, `.env.production`, or any other file that does
  get committed.
- Rotate tokens from the board settings drawer if a token leaks;
  revocation is instant.
- Prefer per-agent or per-environment tokens if your deployment supports
  it (`MOOTASKS_TOKEN_JUNIE`, `MOOTASKS_TOKEN_CLAUDE`, etc.).
- Never paste the token into a Mootasks comment, a commit message, an
  error report, or a log line.
- Public boards (`mcpPublic = true`) skip auth entirely and should only
  be used for read-only demos.
