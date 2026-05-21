#!/usr/bin/env bash
set -euo pipefail

run_artisan() {
  if [[ -x ./vendor/bin/sail && "${LARAVEL_SAIL:-0}" != "1" ]]; then
    ./vendor/bin/sail php artisan test "$@"
  else
    php artisan test "$@"
  fi
}

if [[ $# -eq 0 ]]; then
  run_artisan --stop-on-failure
  exit 0
fi

arg="$1"
shift || true

if [[ -f "$arg" ]]; then
  run_artisan "$arg" "$@"
  exit 0
fi

candidate=""
if command -v rg >/dev/null 2>&1; then
  candidate=$(rg --files -g "${arg}.php" -g "${arg}Test.php" Modules tests | head -n 1)
else
  candidate=$(find Modules tests \( -name "${arg}.php" -o -name "${arg}Test.php" \) | head -n 1)
fi

if [[ -n "$candidate" ]]; then
  run_artisan "$candidate" "$@"
  exit 0
fi

run_artisan --filter "$arg" "$@"
