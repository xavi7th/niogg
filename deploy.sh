#!/usr/bin/env bash
set -euo pipefail

# ====== CONFIG ======
ENVIRONMENT="${1:-production}"
SSH_ALIAS="niogg-server"

if [ "$ENVIRONMENT" = "production" ]; then
  DOMAIN="niogg.org"
  BASE="/home/tokkejlb/niogg.org"
  HEALTH_CHECK_URL="https://niogg.org"
elif [ "$ENVIRONMENT" = "staging" ]; then
  DOMAIN="develop.niogg.org"
  BASE="/home/tokkejlb/develop.niogg.org"
  HEALTH_CHECK_URL="https://develop.niogg.org"
else
  echo "❌ Error: Invalid environment '$ENVIRONMENT'. Use 'staging' or 'production'."
  exit 1
fi
# Branch check for production
# if [ "$ENVIRONMENT" = "production" ]; then
#   CURRENT_BRANCH=$(git branch --show-current)
#   if [ "$CURRENT_BRANCH" != "master" ]; then
#     echo "❌ Error: Production deployments must be run from the 'master' branch."
#     echo "   Current branch: $CURRENT_BRANCH"
#     exit 1
#   fi
# fi

# Build locally
BUILD_CMD=("bun" "run" "build")

# How many releases to keep
KEEP_RELEASES="${KEEP_RELEASES:-5}"

# Dry-run mode:
#   DEPLOY_DRY_RUN=1 ./deploy.sh
DEPLOY_DRY_RUN="${DEPLOY_DRY_RUN:-0}"

# Optional: skip build step
SKIP_BUILD="${SKIP_BUILD:-0}"

# Excludes for rsync
EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=*.tgz"
  "--exclude=/docker/"
  "--exclude=/tests/"
  "--exclude=/test-results/"
  "--exclude=/vendor/"
  "--exclude=/.cache/"
  "--exclude=/bootstrap/cache/*.php"
  "--exclude=/public/storage/"
  "--exclude=/public/hot/"
  "--exclude=/.planning/"
  "--exclude=/storage/"
  "--exclude=/.env"
  "--exclude=/node_modules/"
  "--exclude=/Modules/PublicPage/resources/template/"
  "--exclude=/template/"
  "--exclude=/.git/"
  "--exclude=/.github/"
  "--exclude=.claude/"
  "--exclude=.codex/"
  "--exclude=current_issues.json"
  "--exclude=deploy.js"
  "--exclude=deploy-sample.sh"
  "--exclude=DEPLOYMENT-sample.md"
)

# ====================

# -a: archive mode
# -z: compress during transfer
# -c: checksum (crucial for your build assets issue)
# -h: human readable numbers
# --delete: remove files in destination not in source
RSYNC_ARGS=(-azch --delete)

# We remove -c (checksum) because it's slow and use --size-only
# We add --ignore-times so rsync doesn't skip files just because times match,
# but combined with --link-dest, it allows linking identical-sized files.
# RSYNC_ARGS=(-az --size-only --delete)

# And if you want to see exactly WHY a file is being moved,
# change the itemize flag temporarily to see more detail:
RSYNC_ITEMIZE=(-iv --stats)
SSH_MKDIR_CMD="mkdir -p"
REMOTE_FINALIZE_MODE="RUN"

if [ "$DEPLOY_DRY_RUN" = "1" ]; then
  echo "🟡 DRY-RUN MODE enabled: rsync will NOT write, server finalize will NOT run."
  RSYNC_ARGS=(-azn --delete)  # -n = dry run
  RSYNC_ITEMIZE=(-i -v)       # show changes
  SSH_MKDIR_CMD="echo [DRY-RUN] mkdir -p"
  REMOTE_FINALIZE_MODE="DRY"
fi

TS="$(date +%Y%m%d_%H%M%S)"
REL="$BASE/releases/$TS"

echo "==> [0/7] Environment: $ENVIRONMENT, Mode: DEPLOY_DRY_RUN=$DEPLOY_DRY_RUN, SKIP_BUILD=$SKIP_BUILD"
echo "==> Target release: $REL"

if [ "$SKIP_BUILD" != "1" ]; then
  echo "==> [1/7] Build assets locally: ${BUILD_CMD[*]}"
  "${BUILD_CMD[@]}"
else
  echo "==> [1/7] Skipping local build (SKIP_BUILD=1)"
fi

echo "==> [2/7] Create release dir on server: $REL"
ssh "$SSH_ALIAS" "$SSH_MKDIR_CMD '$REL'"

echo "==> [3/7] Rsync files (deltas via --link-dest when current exists)"
if ssh "$SSH_ALIAS" "[ -L '$BASE/current' ]"; then
  LINKDEST=(--link-dest="$BASE/current")
else
  LINKDEST=()
fi

rsync "${RSYNC_ARGS[@]}" "${RSYNC_ITEMIZE[@]}" \
  ${LINKDEST[@]+"${LINKDEST[@]}"} \
  "${EXCLUDES[@]}" ./ "$SSH_ALIAS:$REL/"

echo "==> [4/7] Finalize on server (shared links, composer, migrations, switch current)"
if [ "$REMOTE_FINALIZE_MODE" = "DRY" ]; then
  echo "==> [DRY-RUN] Skipping remote finalize."
else
  ssh "$SSH_ALIAS" bash <<EOSSH
set -euo pipefail

BASE="$BASE"
REL="$REL"

cd "\$BASE"

# Link shared .env
echo "  → Linking .env..."
ln -sfn "\$BASE/shared/env/.env" "\$REL/.env"

# Link shared storage
echo "  → Linking storage..."
rm -rf "\$REL/storage"
ln -sfn "\$BASE/shared/storage" "\$REL/storage"

# Ensure shared storage structure
mkdir -p "\$BASE/shared/storage/app/public"
mkdir -p "\$BASE/shared/storage/framework/cache/data"
mkdir -p "\$BASE/shared/storage/framework/sessions"
mkdir -p "\$BASE/shared/storage/framework/testing"
mkdir -p "\$BASE/shared/storage/framework/views"
mkdir -p "\$BASE/shared/storage/logs"

# Link shared vendor
echo "  → Linking vendor..."
rm -rf "\$REL/vendor"
ln -sfn "\$BASE/shared/vendor" "\$REL/vendor"

cd "\$REL"

# Ensure bootstrap/cache exists before composer runs
mkdir -p bootstrap/cache

# Install dependencies
echo "  → Installing Compoesr dependencies..."
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Run migrations
echo "  → Running database migrations..."
if ! php artisan migrate --force; then
  echo "❌ ERROR: Database migration failed!"
  exit 1
fi

# Optimize caches
echo "  → Optimizing caches..."
composer recompile

# Atomic switch
echo "  → Switching current release..."
ln -sfn "\$REL" "\$BASE/current"

# Ensure public symlink (the one the web server points to)
echo "  → Updating public symlink..."
rm -rf "\$BASE/public"
ln -sfn "\$BASE/current/public" "\$BASE/public"

# Fix public/build permissions for web access
echo "  → Fixing public/build permissions..."
find "\$REL/public/build" -type d -exec chmod 755 {} \; 2>/dev/null || true
find "\$REL/public/build" -type f -exec chmod 644 {} \; 2>/dev/null || true

echo "✅ Server finalize complete"
EOSSH
fi

echo "==> [5/7] Post-deploy hooks (queue restart, opcache reset)"
if [ "$REMOTE_FINALIZE_MODE" != "DRY" ]; then
  ssh "$SSH_ALIAS" bash <<EOSSH
set -euo pipefail
cd "$BASE/current"

# Restart queue
echo "  → Restarting queue..."
php artisan queue:restart || true

echo "✅ Post-deploy hooks complete"
EOSSH
fi

# Mark release as OK
if [ "$REMOTE_FINALIZE_MODE" != "DRY" ]; then
  ssh "$SSH_ALIAS" "touch '$REL/.ok'"
fi

echo "==> [6/7] Cleanup old releases (keeping last $KEEP_RELEASES)"
if [ "$REMOTE_FINALIZE_MODE" != "DRY" ]; then
  ssh "$SSH_ALIAS" bash <<EOSSH
set -euo pipefail
BASE="$BASE"
KEEP="$KEEP_RELEASES"

# Find all successful releases sorted by date
mapfile -t SUCCESSFUL < <(find "\$BASE/releases" -maxdepth 1 -type d -exec test -e "{}/.ok" \; -print | sort -r)

# Find all releases
mapfile -t ALL < <(ls -1d "\$BASE/releases"/* 2>/dev/null)

CURRENT_REAL="\$(readlink -f "\$BASE/current")"

for rel in "\${ALL[@]}"; do
  KEEP_THIS=0
  # Keep if in top N successful
  for k in "\${SUCCESSFUL[@]:0:\$KEEP}"; do
    [ "\$rel" = "\$k" ] && KEEP_THIS=1 && break
  done
  # Always keep current
  [ "\$rel" = "\$CURRENT_REAL" ] && KEEP_THIS=1

  if [ "\$KEEP_THIS" = "0" ]; then
    echo "  → Removing old release: \$(basename "\$rel")"
    rm -rf "\$rel"
  fi
done
EOSSH
fi

echo "==> [7/7] Health check: $HEALTH_CHECK_URL"
if [ "$REMOTE_FINALIZE_MODE" != "DRY" ]; then
  if curl -sSf -o /dev/null -w "HTTP %{http_code}\n" "$HEALTH_CHECK_URL" --max-time 10; then
    echo "✅ Health check passed!"
  else
    echo "⚠️  Health check failed - please verify manually"
  fi
fi

echo ""
echo "======================================"
echo "✅ DEPLOYMENT COMPLETE ($ENVIRONMENT)"
echo "======================================"
