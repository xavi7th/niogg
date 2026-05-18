#!/bin/bash
cleanup() {
    echo ""
    echo "Stopping containers..."
    sail stop
}
trap cleanup EXIT

bunx concurrently -c "#93c5fd,#c4b5fd,#34d399,#fb7185,#fbbf24" \
  "sail up" \
  "(sleep 15 && sail bun run dev)" \
  "(sleep 30 && sail artisan queue:listen --tries=1 --timeout=0)" \
  "(sleep 30 && sail artisan pail --timeout=0)" \
  "(sleep 60 && sail artisan schedule:work)" \
  --names=sail,vite,queue,logs,schedule \
  --kill-others \
  --restart-tries=0
