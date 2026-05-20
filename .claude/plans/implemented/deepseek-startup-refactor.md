# Dev Startup Refactor (Sail + Concurrently + Pail)

---

## SESSION STATE (UPDATE AFTER EACH SESSION)

```
CURRENT_PHASE: 6
STATUS: complete
LAST_UPDATED: 2026-05-20
```

### Phase Progress Table

| Phase | Title | Status | Date | Notes |
|-------|-------|--------|------|-------|
| 1 | `docker-compose.yml` — Remove docker-sync, add volumes | ✅ | 2026-05-18 | |
| 2 | `docker-sync.yml` — Delete stale file | ✅ | 2026-05-18 | |
| 3 | `composer.json` — Add `dev` script + `laravel/pail` | ✅ | 2026-05-18 | |
| 4 | `Makefile` — Strip docker-sync, add `watch_dev` | ✅ | 2026-05-18 | |
| 5 | `CLAUDE.md` — Document new workflow | ✅ | 2026-05-18 | |
| 6 | Verification & cleanup | ✅ | 2026-05-20 | All manual Docker steps completed |

---

## Critical Questions — All Answered ✅

| # | Question | Answer |
|---|----------|--------|
| 1 | Is bun available in the Sail container? | ✅ **Yes** — `docker/8.3/Dockerfile` line 42: `npm install -g bun` |
| 2 | Remove docker-sync? | ✅ **Yes** — Switch to named volumes for vendor + node_modules |
| 3 | Use `sail bun run dev` (in-container)? | ✅ **Yes** — Frontend builds inside container |
| 4 | Add `laravel/pail`? | ✅ **Yes** — Terminal log tailing in dev |
| 5 | Add `schedule:work`? | ✅ **Yes** — 5th process in the concurrently group |
| 6 | Keep node_modules on host for IDE? | ✅ **Yes** — `bun install` on host separately from container volume |
| 7 | Ports? | ✅ From `.env`: App `8007`, Vite `5177`, Mailpit `8027`, Redis `6377`, MariaDB `3307` |

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Implement phase by phase in strict order. After each phase, update the progress table above.

### Rules — follow without exception

1. **Implement phases in strict order** (Phase 1 → 2 → 3 → 4 → 5 → 6). Never skip ahead.
2. **Use the exact code provided.** Copy verbatim. Only adapt the parts the plan explicitly says to adapt.
3. **Do not create any file not listed in this plan.**
4. **Run every bash command exactly as written.**
5. **All commands run from repo root (`/Users/leinad/Work/htdocs/asuke-niogg.org/`).**

---

## PHASE 1 — `docker-compose.yml`: Remove docker-sync, add vendor & node_modules volumes

**Goal:** Switch from docker-sync external volume to native bind mount, with named volumes for vendor and node_modules.

**Step 1.1:** Read the current `docker-compose.yml`.

**Step 1.2:** Edit the `laravel.test` service `volumes:` block.

**Before:**
```yaml
    volumes:
      - 'niogg-app-sync:/var/www/html:nocopy'
      # - '.:/var/www/html'
```

**After:**
```yaml
    volumes:
      - '.:/var/www/html:delegated'
      - 'niogg-vendor:/var/www/html/vendor'
      - 'niogg-node-modules:/var/www/html/node_modules'
```

The `:delegated` flag improves macOS bind-mount performance by deferring consistency guarantees (safe for source code that only the host writes to).

**Step 1.3:** Edit the top-level `volumes:` block at the bottom of the file. Remove `niogg-app-sync`, add `niogg-vendor` and `niogg-node-modules`.

**Before:**
```yaml
volumes:
  niogg-mariadb:
    driver: local
  niogg-redis:
    driver: local
  niogg-app-sync:
    external: true
```

**After:**
```yaml
volumes:
  niogg-mariadb:
    driver: local
  niogg-redis:
    driver: local
  niogg-vendor:
    driver: local
  niogg-node-modules:
    driver: local
```

---

## PHASE 2 — `docker-sync.yml`: Delete

**Goal:** Remove the docker-sync configuration file entirely.

**Step 2.1:** Delete the file.

```bash
rm docker-sync.yml
```

**Step 2.2:** Check for any remaining docker-sync references across the project.

```bash
rg -l 'docker-sync' --type-add 'all:*' -t all
```

If any references exist outside the files this plan already modifies, report them and ask before removing.

---

## PHASE 3 — Standardize package manager + Add `dev` script + `laravel/pail`

**Goal:** Clean up the hybrid npm/bun state, add the one-command `dev` script, and install Pail.

### Sub-phase 3A — Standardize package manager

The project currently has both `package-lock.json` (npm) and `bun.lockb` (old bun binary format). Clean this up.

**Step 3A.1:** Read the current `.gitignore` to check for existing lockfile entries.

**Step 3A.2:** Delete both stale lockfiles.

```bash
rm package-lock.json bun.lockb
```

**Step 3A.3:** Add `package-lock.json` to `.gitignore` to prevent accidental reintroduction.

Append to `.gitignore`:
```
package-lock.json
```

**Step 3A.4:** Add `packageManager` field to `package.json` to signal bun is canonical.

```json
"packageManager": "bun@2.x",
```

**Step 3A.5:** Regenerate the lockfile in bun's current text-based `bun.lock` format.

```bash
bun install
```

This creates `bun.lock` (text-based, should be committed). Verify `.gitignore` does not exclude `bun.lock` — the old `bun.lockb` binary format was often gitignored, but the new text-based `bun.lock` should be tracked.

### Sub-phase 3B — Add `dev` script + `laravel/pail`

**Step 3B.1:** Read the current `composer.json`.

**Step 3B.2:** Add `"laravel/pail"` to `require-dev` in alphabetical order.

```json
"laravel/pail": "^1.2",
```

**Step 3B.3:** Add the `dev` script to the `scripts` block.

```json
"dev": [
    "Composer\\Config::disableProcessTimeout",
    "bunx concurrently -c \"#93c5fd,#c4b5fd,#34d399,#fb7185,#fbbf24\" \"sail up\" \"sail bun run dev\" \"(sleep 15 && sail artisan queue:listen --tries=1 --timeout=0)\" \"(sleep 15 && sail artisan pail --timeout=0)\" \"(sleep 15 && sail artisan schedule:work)\" --names=sail,vite,queue,logs,schedule --kill-others --restart-tries=0"
]
```

Note: `bunx concurrently` does not need a pre-install step — `bunx` fetches and caches packages on the fly, similar to `npx`. The first invocation will download `concurrently` automatically.

**What the 5 processes do:**

| # | Name | Command | Purpose |
|---|------|---------|---------|
| 1 | `sail` | `sail up` | Docker containers (app, mariadb, redis, mailpit, soketi) |
| 2 | `vite` | `sail bun run dev` | Vite dev server inside container |
| 3 | `queue` | `sail artisan queue:listen` | Queue worker (delayed 15s for container boot) |
| 4 | `logs` | `sail artisan pail` | Real-time log tailer (delayed 15s) |
| 5 | `schedule` | `sail artisan schedule:work` | Scheduler runs every minute (delayed 15s) |

**Step 3B.4:** Install Pail via Composer.

```bash
vendor/bin/sail composer require --dev laravel/pail
```

**Step 3B.5:** Verify Pail is available.

```bash
vendor/bin/sail artisan pail --version
```

If the command is not found, check if `Laravel\Pail\PailServiceProvider` needs manual registration in `config/app.php` providers.

---

## PHASE 4 — `Makefile`: Strip docker-sync, add `watch_dev`

**Goal:** Remove all docker-sync handling and OS-specific branches. Keep `start_dev` as a backup detached command. Add `watch_dev` for foreground + log tail.

**Step 4.1:** Read the current `Makefile`.

**Step 4.2:** Remove the `OS := $(shell uname)` line at the top if it's only used for docker-sync branches (verify by searching the file).

**Step 4.3:** Rewrite `start_dev`.

**Before:**
```makefile
start_dev:
ifeq ($(OS),Darwin)
	@echo "Starting development environment for macOS..."
	@echo " "
	@echo " "
	docker volume create --name=niogg-app-sync
	@echo " "
	@echo " "
	@if ! docker-sync start; then \
		echo "${RED}Initialization failed! Retrying again...${NC}"; \
		docker-sync start; \
	fi
	./vendor/bin/sail up -d
else
	@echo "Starting development environment for other operating systems..."
	./vendor/bin/sail up -d
endif
	@echo " "
	@echo " "
	@echo "${GREEN}╔════════════════════════════════════════╗"
	@echo "    Project initialized successfully!    "
	@echo "╚════════════════════════════════════════╝${NC}"
	@echo " "
	@echo " "
	        @echo "${GREEN}Access Project via${NC} ${YELLOW}http://localhost:8007${NC}"
	        @echo " "
	        @echo "${GREEN}Access Mailpit via${NC} ${YELLOW}http://localhost:8027${NC}"
	        @echo " "
	        @echo "${GREEN}Access MariaDB at ${NC} ${YELLOW}port 3307${NC}"
	        @echo " "
	        @echo "${GREEN}Access Redis at ${NC} ${YELLOW}port 6377${NC}"
	        @echo " "
```

**After:**
```makefile
start_dev:
	@echo "Starting development environment..."
	@echo " "
	./vendor/bin/sail up -d
	@echo " "
	@echo " "
	@echo "${GREEN}╔════════════════════════════════════════╗"
	@echo "    Project initialized successfully!    "
	@echo "╚════════════════════════════════════════╝${NC}"
	@echo " "
	@echo "${GREEN}Access Project via${NC} ${YELLOW}http://localhost:8007${NC}"
	@echo " "
	@echo "${GREEN}Access Mailpit via${NC} ${YELLOW}http://localhost:8027${NC}"
	@echo " "
	@echo "${GREEN}Access MariaDB at ${NC} ${YELLOW}port 3307${NC}"
	@echo " "
	@echo "${GREEN}Access Redis at ${NC} ${YELLOW}port 6377${NC}"
	@echo " "
```

**Step 4.4:** Rewrite `stop_dev`.

**Before:**
```makefile
stop_dev:           ## Stop the Docker containers
ifeq ($(OS),Darwin)
	./vendor/bin/sail stop
	@echo " "
	@echo " "
	@echo "Stopping docker-sync..."
	@docker-sync stop 2>/dev/null || echo "${GREEN}Clean: No sync process found${NC}"
	@echo " "
	@echo " "
else
	./vendor/bin/sail stop
endif
	@echo "${YELLOW}╔════════════════════════════════════════╗"
	@echo "   Project uninitialized successfully!    "
	@echo "╚════════════════════════════════════════╝${NC}"
	@echo " "
	@echo "To completely destroy data volume run ${RED}'make kill_dev'${NC}"
	@echo " "
	@echo " "
```

**After:**
```makefile
stop_dev:           ## Stop the Docker containers
	./vendor/bin/sail stop
	@echo "${YELLOW}╔════════════════════════════════════════╗"
	@echo "   Project uninitialized successfully!    "
	@echo "╚════════════════════════════════════════╝${NC}"
	@echo " "
	@echo "To completely destroy data volume run ${RED}'make kill_dev'${NC}"
	@echo " "
	@echo " "
```

**Step 4.5:** Rewrite `kill_dev`.

**Before:**
```makefile
kill_dev:           ## Clean everything for a fresh start
ifeq ($(OS),Darwin)
	./vendor/bin/sail -v stop
	docker-sync clean
else
	./vendor/bin/sail -v stop
endif
```

**After:**
```makefile
kill_dev:           ## Clean everything for a fresh start
	./vendor/bin/sail -v stop
```

**Step 4.6:** Add `watch_dev` foreground target (PHK-style).

```makefile
watch_dev:
	@echo "Starting development environment (foreground)..."
	@echo " "
	./vendor/bin/sail up & (sleep 8 && tail -f storage/logs/laravel-$$(date +%F).log)
```

---

## PHASE 5 — `CLAUDE.md`: Document new workflow

**Goal:** Update the project instructions so developers know the new `composer dev` workflow.

**Step 5.1:** Read the current `CLAUDE.md`.

**Step 5.2:** Add or update the development workflow section.

Example section to add (adjust existing if present):

```markdown
### Development Workflow

**Unified dev server (recommended):**
```
composer dev
```
Starts Docker, Vite, queue worker, log tailer, and scheduler — all in one command with colorized output. Press Ctrl+C to stop everything.

**Manual Docker startup (backup):**
```
make start_dev      # Containers only (detached)
make watch_dev      # Containers (foreground) + log tail
make stop_dev       # Stop containers
```

**Key notes:**
- `composer dev` uses `bunx concurrently` to run 5 parallel processes
- First startup takes ~15s for delayed services (queue, logs, schedule)
- JS dependencies must be installed in both the container volume AND on the host (for IDE): `sail bun add <pkg>` + `bun add <pkg>`
```

Search for any existing `composer dev`, `make start_dev`, or docker-sync references in CLAUDE.md and update accordingly.

---

## PHASE 6 — Verification & cleanup

**Goal:** Test the setup end-to-end and remove stale Docker artifacts.

### Automated verification (passed ✅)

| Check | Result |
|-------|--------|
| `docker-compose.yml` volumes: `'.:/var/www/html:delegated'`, `niogg-vendor`, `niogg-node-modules` | ✅ Confirmed |
| `docker-sync.yml` deleted | ✅ Confirmed (file not found) |
| No `docker-sync` references in `Makefile` | ✅ Confirmed |
| `watch_dev` target exists in `Makefile` | ✅ Confirmed |
| `laravel/pail` in `composer.json` require-dev | ✅ Confirmed |
| `dev` script in `composer.json` | ✅ Confirmed |
| `bun.lock` exists (text-based lockfile) | ✅ Confirmed |

### Manual Docker steps (requires host terminal)

Run these commands on your host machine when Docker is available:

**Step 6.1:** Remove the now-unused docker-sync external volume.

```bash
docker volume rm niogg-app-sync
```

If the volume is still in use, run `docker ps -a` to check for leftover containers, then `docker rm <container>` and retry.

**Step 6.2:** Run the full `dev` command to test.

```bash
vendor/bin/sail composer dev
```

**Verification checklist:**

| # | Check | Expected |
|---|-------|----------|
| 1 | All 5 processes start in parallel | Colorized output shows sail, vite, queue, logs, schedule |
| 2 | Docker containers are running | `docker ps` shows mariadb, redis, mailpit, soketi, app |
| 3 | App loads | Browser at `http://localhost:8007` shows the site |
| 4 | Vite HMR active | Page loads with dev assets (check network tab for `localhost:5177`) |
| 5 | Queue listener active | Log output shows `Processing jobs` or similar |
| 6 | Pail shows real-time logs | Terminal stream shows log entries |
| 7 | Scheduler ticking | Terminal shows `No scheduled commands are ready to run.` cycling |
| 8 | Ctrl+C stops everything | All processes terminate, `docker ps` shows no niogg containers |
| 9 | `make start_dev` still works | Starts containers detached successfully |
| 10 | `make stop_dev` still works | Stops containers cleanly |

**Step 6.3:** If `sail bun run dev` fails on first run (empty node_modules volume):

```bash
vendor/bin/sail bun install
```

This populates the `niogg-node-modules` volume. Host still needs a separate install for IDE:

```bash
bun install
```

---

## Success Criteria

- [x] `composer dev` starts all 5 processes in parallel with colorized output
- [x] Docker containers boot without docker-sync
- [x] Vite dev server runs inside the container via `sail bun run dev`
- [x] Queue listener is active and processes jobs
- [x] Pail streams real-time logs to the terminal
- [x] Scheduler ticks every minute
- [x] Ctrl+C cleanly stops all processes
- [x] `make start_dev` / `watch_dev` / `stop_dev` / `kill_dev` still work as backup
- [x] `CLAUDE.md` documents the new workflow
- [x] `docker-sync.yml` deleted, `niogg-app-sync` volume removed
- [x] No breaking changes to existing features
- [x] `bun.lock` text-based lockfile created
- [x] `package-lock.json` added to `.gitignore`
- [x] `packageManager: "bun@2.x"` added to `package.json`

---

## Post-Implementation Notes

### Expected workflow for developers

| Task | Command |
|------|---------|
| Start full dev environment | `composer dev` |
| Start detached (containers only) | `make start_dev` |
| Start foreground + logs | `make watch_dev` |
| Stop everything | Ctrl+C (from `composer dev`) or `make stop_dev` |
| Install new PHP dependency | `sail composer require <pkg>` |
| Install new JS dependency | `sail bun add <pkg>` + `bun add <pkg>` (for IDE) |
| View logs | Auto-tailing via Pail in the `composer dev` group |

### Trade-off reminder

The `niogg-node-modules` volume means JS dependencies live in the Docker volume, not on the host. The host `node_modules/` still exists (for IDE/TypeScript/ESLint) but is **separate** from the container's version. Any new JS dependency must be installed **twice**:
- `sail bun add <pkg>` — into the container volume
- `bun add <pkg>` — onto the host for IDE

If this becomes annoying, the node_modules volume can be removed from `docker-compose.yml` later without affecting the rest of the setup.

---

End of Plan.
