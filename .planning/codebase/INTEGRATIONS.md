# External Integrations

**Analysis Date:** 2026-02-02

## APIs & External Services

**Real-time Communication:**

- Pusher/Soketi - WebSockets for real-time features
  - Soketi container (quay.io/soketi/soketi:latest-16-alpine)
  - Ports: 6001 (WebSocket), 9601 (metrics)
  - Auth via environment variables: PUSHER_APP_KEY, PUSHER_APP_SECRET

**Email Services:**

- Mailpit - Local email development/testing
  - Container: axllent/mailpit:latest
  - Ports: 1025 (SMTP), 8025 (dashboard)
  - Configured in MAIL\_\* environment variables

**Media Processing:**

- AWS S3 - File storage (optional)
  - Configured via AWS\_\* environment variables
  - Endpoint: us-east-1
  - Bucket name via AWS_BUCKET

## Data Storage

**Databases:**

- MariaDB 10 - Primary relational database
  - Connection: DB\_\* environment variables
  - Database: test (development)
  - User: sail, password: password (in Docker)

**Caching:**

- Redis - Session storage and caching
  - Connection: REDIS_HOST, REDIS_PORT (6377 in dev)
  - Session driver configured in Laravel

## Authentication & Identity

**Auth Provider:**

- Laravel Sanctum - API token authentication
  - Implementation: Custom token generation via Sanctum
  - Used for API endpoints and SPA authentication

**User Management:**

- Custom authentication system via UserAuth module
- Laravel Sanctum for API authentication tokens
- Session-based authentication for web interface

## Monitoring & Observability

**Error Tracking:**

- Laravel Ignition - Local development debugging
  - Editor: VSCode configured via IGNITION_EDITOR
  - Theme: Dark mode configured via IGNITION_THEME
- OpCodes Log Viewer - Production log viewing
  - Route: /administrative-logs
  - Enabled via LOG_VIEWER_ENABLED=true

**Logs:**

- File logging (single channel)
- Debug level for development
- Deprecation tracking enabled

## CI/CD & Deployment

**Hosting:**

- Docker containers via Laravel Sail
- Docker-sync for MacOS performance optimization

**Development Workflow:**

- Pre-commit hooks for linting and formatting
- Custom build scripts in composer.json
- Module-based asset loading

## Environment Configuration

**Required env vars:**

- APP\_\* - Application settings (name, URL, contact info)
- DB\_\* - Database connection (host, port, credentials)
- REDIS\_\* - Redis connection settings
- MAIL\_\* - Email configuration
- AWS\_\* - S3 storage (if used)
- PUSHER\_\* - WebSocket configuration
- VITE\_\* - Frontend environment variables

**Secrets location:**

- `.env` file (excluded from git)
- Docker Compose configuration
- Laravel Sail environment setup

## Webhooks & Callbacks

**Incoming:**

- None detected in configuration

**Outgoing:**

- None detected in configuration
- Soketi can handle outgoing WebSocket events

---

_Integration audit: 2026-02-02_

```

```
