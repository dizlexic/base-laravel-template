# Docker, Sail & Horizon

> Read [`/AGENTS.md`](../../AGENTS.md) first. This file documents the
> container conventions: Sail for local dev, the additional `phpmyadmin`
> service, and how `php artisan horizon` is supervised inside the production
> image.

---

## 1. Local development = Laravel Sail

All commands run inside the dev container. Pick one of:

```bash
./vendor/bin/sail <cmd>           # preferred
docker compose exec laravel.test <cmd>
docker exec -it <container> <cmd>
```

Common everyday calls:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail down
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker
./vendor/bin/sail composer require <pkg>
./vendor/bin/sail npm run dev
./vendor/bin/sail test
./vendor/bin/sail dusk
./vendor/bin/sail shell           # bash inside the laravel.test container
```

> **Never** run `php`, `composer`, `npm`, `pnpm`, `node`, `artisan`, or
> `phpunit` on the host. The host PHP/Node versions are not guaranteed to
> match the container, and `vendor/` paths assume the container's filesystem
> layout.

---

## 2. Compose services

The default Sail-generated stack (in `compose.yaml`) provides:

| Service        | Image                          | Purpose                             |
|----------------|--------------------------------|-------------------------------------|
| `laravel.test` | `sail-8.5/app` (built locally) | The app container (PHP-FPM + nginx) |
| `mysql`        | `mysql:8.4`                    | Primary database                    |
| `redis`        | `redis:alpine`                 | Cache + queue + session backend     |
| `selenium`     | `selenium/standalone-chromium` | Dusk browser tests                  |
| `mailpit`      | `axllent/mailpit:latest`       | Captures outbound mail in dev       |

### 2.1 phpMyAdmin (added)

`compose.yaml` is extended with a `phpmyadmin` service so humans can browse
the dev database without installing a desktop client. Suggested service
definition:

```yaml
phpmyadmin:
    image: 'phpmyadmin:latest'
    ports:
        - '${FORWARD_PHPMYADMIN_PORT:-8080}:80'
    environment:
        PMA_HOST: mysql
        PMA_PORT: 3306
        PMA_USER: '${DB_USERNAME}'
        PMA_PASSWORD: '${DB_PASSWORD}'
        UPLOAD_LIMIT: 256M
    networks:
        - sail
    depends_on:
        - mysql
```

Then add `FORWARD_PHPMYADMIN_PORT=8080` to `.env.example`. Browse it at
<http://localhost:8080>. phpMyAdmin is a *local-dev-only* convenience —
never include it in a production compose / k8s deployment.

---

## 3. The published Dockerfile lives at `docker/8.5/`

The Sail runtime under `vendor/laravel/sail/runtimes/8.5` is fine for
upstream defaults, but to add Horizon supervision (and anything else
project-specific) we **published** the Dockerfile out of `vendor/` into:

```
docker/8.5/
├── Dockerfile          # patched: installs systemd, enables our units
├── start-container     # original Sail entrypoint, now invoked by sail-app.service
├── supervisord.conf    # original, still drives the `program:php` block
├── php.ini
├── sail-app.service    # systemd unit wrapping start-container
└── horizon.service     # systemd unit for `php artisan horizon`
```

`compose.yaml` builds from this directory (`build.context: ./docker/8.5`,
`image: app-8.5/app`). The files are ours to edit — never touch anything
inside `vendor/`.

To regenerate the Sail-shipped pieces from a newer Sail release, copy the
upstream files back over the local copies and re-apply the patches in §4
(install systemd, copy the units, switch entrypoint to `/sbin/init`).

---

## 4. Running Horizon under systemd inside the container

`php artisan horizon` is a long-running supervisor. It must:

- Start when the container starts.
- Be restarted automatically on crash.
- Receive `SIGTERM` cleanly so deploys don't drop in-flight jobs.

Per the original project brief, this template runs Horizon as a
**systemd** service inside the app container. systemd is therefore PID 1
in the image; both `php artisan horizon` and the existing
`start-container`/supervisord PHP serve loop are managed as systemd
units.

### 4.1 The two units we ship

`docker/8.5/sail-app.service` keeps the original Sail PHP serve process
alive (it execs the same `start-container` script Sail has always used,
which in turn runs `supervisord -n`):

```ini
[Unit]
Description=Sail application (PHP serve via supervisord)
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
WorkingDirectory=/var/www/html
EnvironmentFile=-/var/www/html/.env
ExecStart=/usr/local/bin/start-container
Restart=always
RestartSec=5
KillMode=mixed
KillSignal=SIGTERM
TimeoutStopSec=60
StandardOutput=journal
StandardError=journal
SyslogIdentifier=sail-app

[Install]
WantedBy=multi-user.target
```

`docker/8.5/horizon.service` runs Horizon itself, with the canonical
`horizon:terminate` graceful-shutdown command bound to `ExecStop`:

```ini
[Unit]
Description=Laravel Horizon (queue supervisor)
After=network-online.target sail-app.service
Wants=network-online.target

[Service]
Type=simple
User=sail
Group=sail
WorkingDirectory=/var/www/html
EnvironmentFile=-/var/www/html/.env
ExecStart=/usr/bin/php /var/www/html/artisan horizon
ExecStop=/usr/bin/php /var/www/html/artisan horizon:terminate
Restart=always
RestartSec=5
KillMode=mixed
KillSignal=SIGTERM
TimeoutStopSec=3600
StandardOutput=journal
StandardError=journal
SyslogIdentifier=horizon

[Install]
WantedBy=multi-user.target
```

Key flags:

- `TimeoutStopSec=3600` — gives Horizon up to an hour to finish in-flight
  jobs on `docker stop` / rolling deploy.
- `KillSignal=SIGTERM` — Horizon listens for `SIGTERM` and shuts down
  gracefully (after `ExecStop` runs `horizon:terminate`).
- `Restart=always` — systemd restarts Horizon on crash.

### 4.2 The Dockerfile patch

`docker/8.5/Dockerfile` installs systemd, masks units that don't make
sense in a container, and switches the entrypoint to `/sbin/init`:

... systemd systemd-sysv dbus ...
```dockerfile
# (added to the existing apt-get install line)

COPY sail-app.service /etc/systemd/system/sail-app.service
COPY horizon.service /etc/systemd/system/horizon.service

RUN systemctl set-default multi-user.target \
    && systemctl mask \
        systemd-udevd.service systemd-udev-trigger.service \
        systemd-firstboot.service systemd-remount-fs.service \
        sys-kernel-config.mount sys-kernel-debug.mount \
        sys-kernel-tracing.mount sys-fs-fuse-connections.mount \
        getty.target getty@.service \
    && systemctl enable sail-app.service horizon.service

STOPSIGNAL SIGRTMIN+3

ENTRYPOINT ["/sbin/init"]
```

`SIGRTMIN+3` is the signal systemd treats as "shut down cleanly", so
`docker stop` triggers a graceful shutdown of every unit (which means
Horizon's `ExecStop=horizon:terminate` runs, which means in-flight jobs
finish before the container exits).

### 4.3 The compose-side requirements

systemd as PID 1 needs cgroup access, a writable `/run`, and the right
stop signal. `compose.yaml`'s `laravel.test` service therefore declares:

```yaml
privileged: true
cgroup: host
stop_signal: SIGRTMIN+3
tmpfs:
    - /run
    - /run/lock
    - /tmp
```

`privileged: true` is the simplest knob for local dev; in production you
can usually narrow it to specific capabilities (`SYS_ADMIN`,
`SYS_NICE`, `NET_ADMIN`) plus `--cgroupns=host` instead.

### 4.4 Day-to-day commands

```bash
# Inside the container (./vendor/bin/sail shell)
systemctl status horizon
systemctl restart horizon
journalctl -u horizon -f                    # tail Horizon logs

./vendor/bin/sail artisan horizon:status    # Horizon's own health view
./vendor/bin/sail artisan horizon:terminate # graceful respawn (deploy hook)
```

Use `horizon:terminate` after every deployment — systemd respawns Horizon
immediately, picking up the new code.

### 4.5 Horizon dashboard

Reachable at `/horizon`. Access is gated by
`Gate::define('viewHorizon', ...)` in `App\Providers\HorizonServiceProvider`.
Restrict it to a specific role/permission in production — never leave it
open.

---

## 5. Telescope in containers

- Telescope is enabled in `local` and `staging` only.
- Telescope writes to the application database. In production, we keep its
  recording filter narrow (`exceptions`, `slow queries`) so the database
  doesn't bloat — see [`tech-stack.md`](tech-stack.md) §Observability.
- The Telescope dashboard at `/telescope` is gated by
  `Gate::define('viewTelescope', ...)`. Restrict to admins.

---

## 6. Quick troubleshooting

| Symptom                                               | First thing to try                                                                                           |
|-------------------------------------------------------|--------------------------------------------------------------------------------------------------------------|
| `permission denied` on `storage/`                     | `./vendor/bin/sail root-shell` then `chown -R sail:sail storage bootstrap/cache`                             |
| Vite assets 404 in dev                                | `./vendor/bin/sail npm run dev` and check `VITE_PORT`                                                        |
| Horizon shows "Inactive"                              | `systemctl status horizon` + `journalctl -u horizon -n 200`; then `./vendor/bin/sail artisan horizon:status` |
| Queue jobs stuck                                      | `./vendor/bin/sail artisan horizon:terminate` to force respawn (systemd restarts it)                         |
| Container won't start (`Failed to mount cgroup` etc.) | Confirm `privileged: true`, `cgroup: host`, and the `/run` tmpfs are present in `compose.yaml`               |
| phpMyAdmin "Cannot connect to mysql"                  | Wait for the `mysql` healthcheck to go green, then refresh                                                   |
| Selenium/Dusk hangs                                   | `./vendor/bin/sail restart selenium`                                                                         |
