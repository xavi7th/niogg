---
name: ralph-prd-to-json
description: "Convert a PRD markdown file or text into a structured prd.json for the Ralph Wiggum autonomous development loop. Extracts user stories from functional requirements, determines verification needs, and outputs machine-readable JSON. Triggers on: convert prd to json, generate prd.json, create ralph json, prd to json, prepare for ralph loop, convert this prd."
context: fork
color: rebeccapurple
---

# PRD to JSON Converter for Ralph Wiggum

You are a converter that transforms PRD markdown documents into structured JSON files that the Ralph Wiggum autonomous development loop can process.

---

## Overview

This skill reads a PRD markdown file from `tasks/` (or accepts direct text input) and converts it into `tasks/prd.json` — a structured JSON file containing user stories that Ralph can iterate through autonomously.

---

## Input Modes

### Mode 1: Convert from File

```
"Convert tasks/prd-authentication.md to prd.json"
```

- **Source:** `tasks/prd-*.md` (the most recent PRD file, or a specific one if named)
- **Fallback:** If no PRD file exists, report an error and stop

### Mode 2: Convert from Text/Clipboard

```
"Convert this PRD to prd.json: [paste PRD content]"
```

- Accepts PRD content directly in the prompt
- Useful for quick conversions without creating a file first

---

## Output

- **Destination:** `tasks/prd.json`
- **Format:** JSON object with project metadata and user stories array

### Output Structure

```json
{
  "project": "[Project Name]",
  "branchName": "feature/[feature-name-kebab-case]",
  "description": "[Feature description from PRD title/intro]",
  "userStories": [
    {
      "id": "US-001",
      "title": "[Story title]",
      "description": "As a [user], I want [feature] so that [benefit]",
      "acceptanceCriteria": ["Criterion 1", "Criterion 2"],
      "priority": 1,
      "passes": false,
      "notes": ""
    }
  ]
}
```

**Note on `branchName`:** This is metadata for the Ralph loop. The `ralph.sh` script handles branch creation/switching before starting execution.

---

## The Number One Rule: Story Size

**Each story must be completable in ONE focused work session (one context window at about 60% capacity).**

If a story is too big, the developer (or AI agent) runs out of focus before finishing and produces incomplete code.

### Right-sized stories:

- Add a database column and migration
- Add a UI component to an existing page
- Update a server action with new logic
- Add a filter dropdown to a list
- Create a single API endpoint

### Too big (split these):

- "Build the entire dashboard" → Split into: schema, queries, UI components, filters
- "Add authentication" → Split into: schema, middleware, login UI, session handling
- "Refactor the API" → Split into one story per endpoint or pattern
- "Add user notification system" → Split into: table, service, UI, preferences

**Rule of thumb:** If you cannot describe the change in 2 sentences, it is too big.

---

## Story Ordering: Dependencies First

Stories execute in priority order. Earlier stories must not depend on later ones.

**Correct order:**

1. Schema/database changes (migrations)
2. Server actions / backend logic
3. UI components that use the backend
4. Dashboard/summary views that aggregate data

**Wrong order:**

1. UI component (depends on schema that doesn't exist yet)
2. Schema change

---

## Conversion Process

### Step 1: Locate the PRD

**For Mode 1 (File):**
1. Check `tasks/` directory for PRD files matching `prd-*.md`
2. If multiple exist, use the most recently modified one (or the one specified)
3. If none exist, return an error: "No PRD file found in tasks/. Please create a PRD first using the ralph-prd-generator skill."

**For Mode 2 (Text):**
1. Use the PRD content provided directly in the prompt

### Step 2: Check for Existing prd.json (Archiving)

Before writing a new prd.json, check if one already exists:

1. Read the current `tasks/prd.json` if it exists
2. Check if `branchName` differs from the new feature's branch name
3. **If different branchName:**
   - Create archive folder: `tasks/archive/YYYY-MM-DD-[branchName]/`
   - Copy current `prd.json`, `progress.txt`, and any `*plan*.md` files to archive
   - Note in output that previous version was archived
4. **If same branchName:**
   - Ask whether to overwrite or append new stories to existing
   - If append, add new stories after existing ones with continued priority numbers

### Step 3: Check Testing Infrastructure

1. Check if `scripts/test.sh` exists in the project
2. Check if `composer.json` contains the `test` and `e2e` scripts
3. If either is missing, flag that US-000 (setup story) needs to be included

### Step 4: Check for Design System & Mockups

Check if the design system skill was run before conversion:

1. Check if `tasks/design-system/tokens.json` exists
2. Check if `tasks/mockups/` directory contains HTML mockups
3. If found, flag that UI stories should reference these files

**If design system exists:**
- UI story acceptance criteria should include: "Match mockup in tasks/mockups/[page].html"
- UI story acceptance criteria should include: "Use design tokens from tasks/design-system/tokens.json"
- Component stories should reference: "Follow patterns in tasks/design-system/components.html"

**If no design system exists:**
- Warn: "No design system found. Consider running 'create design system' before converting."
- Continue conversion without mockup references

### Step 5: Extract Information

From the PRD, extract:

1. **Project/Feature Name** — from the title (used for `project` and `branchName`)
2. **Functional Requirements** — these become the base user stories
3. **Core Features** — additional context for grouping stories
4. **Tech Stack** — informs acceptance criteria (which commands to run, what to test)
5. **UI/UX Requirements** — helps determine which stories need browser verification
6. **Success Criteria** — informs acceptance criteria

### Step 6: Generate User Stories

For each functional requirement (FR-1, FR-2, etc.), create a user story object:

```json
{
  "id": "US-001",
  "title": "Brief descriptive title",
  "description": "As a [user type], I need [functionality] so that [benefit].",
  "acceptanceCriteria": [
    "Specific, verifiable criterion 1",
    "Specific, verifiable criterion 2",
    "Test command that must pass"
  ],
  "priority": 1,
  "passes": false,
  "notes": ""
}
```

#### Story ID Format

- Main stories: `US-001`, `US-002`, etc.
- Browser verification: `US-001-02`
- E2E test stories: `US-001-03`

#### Priority Numbering

- Assign priority based on dependency order (lower = earlier)
- Verification stories get decimal priorities: if main story is priority 5, browser verification is 5.1, E2E is 5.2

### Step 7: Determine Verification Requirements

For each story, analyze what it produces to determine if it needs verification stories.

#### Backend Stories: PEST Tests (MANDATORY)

**ALL backend features MUST have PEST test coverage integrated into the story.**

For backend stories (models, controllers, services, APIs, database changes), include these acceptance criteria:

```
"Write PEST tests in app/Modules/{Module}/Tests/Feature/ or app/Modules/{Module}/Tests/Unit/"
"Tests cover all new functionality and edge cases"
"Tests pass when run with 'composer test'"
```

**Backend stories are NOT complete without passing PEST tests.**

#### Frontend Stories: Add -02 (Browser Verification) when the story:

- Creates or modifies a user-facing page or component
- Changes visual elements (tables, forms, buttons, modals, alerts, notifications)
- Affects layout, styling, or user-visible state
- Requires visual confirmation that it "looks right"
- Involves frontend frameworks (Vue, React, Svelte components)
- Mentions UI elements like "display", "show", "render", "view", "page"

**For ALL main UI stories, include these acceptance criteria:**

```
"Take 'before' screenshot: 'agent-browser screenshot tasks/screenshots/US-XXX-before.png'"
"[Implementation criteria...]"
"Match mockup in tasks/mockups/[page-name].html (if mockup exists)"
"Use design tokens from tasks/design-system/tokens.json (if design system exists)"
"Verify in browser using agent-browser skill"
"If visual issues found: adjust and re-verify until correct"
"Take 'after' screenshot: 'agent-browser screenshot tasks/screenshots/US-XXX-after.png'"
```

**Context Limit for UI Stories**: If context reaches 70% capacity during implementation or verification, STOP immediately. Log challenges to `tasks/progress.txt`. Do NOT mark as passed.

**Design System Integration**: If `tasks/design-system/` exists, UI stories MUST reference:
- `tasks/mockups/[relevant-page].html` — for layout and component reference
- `tasks/design-system/tokens.json` — for colors, typography, spacing
- `tasks/design-system/components.html` — for component patterns

**Browser verification story template:**

```json
{
  "id": "US-001-02",
  "title": "Agent-Browser Verification: [Original Story Title]",
  "description": "Verify [feature] using agent-browser CLI tool for visual confirmation.",
  "acceptanceCriteria": [
    "Navigate using 'agent-browser open <app-url>/[relevant-path]'",
    "Take snapshot with 'agent-browser snapshot -i' to verify elements",
    "Verify [specific visual elements] are displayed correctly",
    "If issues found: adjust implementation, re-verify, repeat until correct",
    "Take screenshot with 'agent-browser screenshot tasks/screenshots/US-001-02-after.png'",
    "Document verification in notes with screenshot path"
  ],
  "priority": 1.1,
  "passes": false,
  "notes": ""
}
```

**Note**: Verify in browser using agent-browser CLI tool as described in the agent-browser skill.

**Verification Loop**: If visual issues are found during verification, adjust the implementation and re-verify. Iterate until the UI matches expectations. Do not mark as passed until visual verification succeeds.

**Context Limit**: If context reaches 70% capacity during verification iterations, STOP immediately. Log challenges and current state to `tasks/progress.txt`. Do NOT mark the story as passed. A new iteration will pick up from the logged state.

#### Frontend Stories: Add -03 (E2E Test) when the story:

- Involves a multi-step user workflow
- Has state changes that persist (form submission, data creation/update/delete)
- Requires interaction sequences (click → fill → submit → verify result)
- Is critical path functionality (authentication, checkout, core CRUD operations)
- Involves complex user interactions with multiple elements
- Has business logic that needs automated regression testing

**When E2E tests may NOT be needed:**
- Simple visual changes (styling updates, layout tweaks)
- Static content updates
- Minor UI adjustments
- Non-interactive components

**E2E test story template:**

```json
{
  "id": "US-001-03",
  "title": "E2E Test: [Original Story Title]",
  "description": "Create an e2e test to verify [feature] works correctly end-to-end.",
  "acceptanceCriteria": [
    "Create a Playwright e2e test in [test-directory]",
    "Test navigates to [relevant page]",
    "Test performs [key user actions]",
    "Test verifies [expected outcomes]",
    "Build assets before testing with '[build command]'",
    "Test must pass when run with 'composer e2e'"
  ],
  "priority": 1.2,
  "passes": false,
  "notes": ""
}
```

**Note**: E2E tests run via `composer e2e` which executes `bun run test:e2e`. [Modify this to match codebase]

**Debugging Note:** If E2E tests fail repeatedly, use the agent-browser CLI tool for debugging:
- Navigate: `agent-browser open <url>`
- Inspect elements: `agent-browser snapshot -i`
- Take screenshots: `agent-browser screenshot /path/to/file.png`
- Check for timing issues or selector problems

#### No verification suffix when the story:

- Is purely backend (API endpoints returning JSON, database migrations, background jobs)
- Is configuration or setup (environment variables, config files)
- Is refactoring with no user-visible changes
- Is documentation or comments only
- Has no UI surface whatsoever

**Note:** Backend stories still require PEST tests as part of the main story's acceptance criteria.

### Step 8: Write Acceptance Criteria

Acceptance criteria must be:

1. **Specific and verifiable** — not vague ("works correctly") but concrete ("returns 200 status code")
2. **Include file paths** — specify exactly where code should be added/modified
3. **Include test commands** — the exact command to verify completion
4. **Self-contained** — an autonomous agent should be able to verify completion without asking questions

#### Good criteria (verifiable):

- "Add `status` column to tasks table with default 'pending'"
- "Filter dropdown has options: All, Active, Completed"
- "Clicking delete shows confirmation dialog"
- "Endpoint returns 401 if user not authenticated"

#### Bad criteria (vague):

- "Works correctly"
- "User can do X easily"
- "Good UX"
- "Handles edge cases"

#### Code Quality Checks (REQUIRED)

**Always add appropriate code quality checks as final criteria based on tech stack:**

**For PHP/Laravel projects:**
```
"Run 'composer lint' to format code"
"Run 'composer test' to verify tests pass"
```

**For TypeScript/JavaScript projects:**
```
"Run 'bun format' to format code"
"Run 'bun run typecheck' to verify types"
```

**For mixed projects (Laravel + Vue/React/Svelte):**
```
"Run 'composer lint' to format PHP code"
"Run 'bun format' to format frontend code"
"Run 'bun run typecheck' to verify types"
"Run 'composer test' to verify tests pass"
```

#### Tech-Stack Specific Commands

Based on the PRD's tech stack, use appropriate commands:

**Laravel:**
- `composer test` - runs PEST tests (auto-detects Sail)
- `composer e2e` - runs E2E tests
- `composer lint` - formats code (Rector + Pint)
- `php artisan migrate`

**Node/JavaScript:**
- `bun run test` or `npm run test`
- `bun run typecheck` or `npm run typecheck`
- `bun run build` or `npm run build`
- `bun format` or `npm run format`

**Vue/React/Svelte:**
- Build command before browser verification
- Typecheck command
- Format command

**Python:**
- `pytest`
- `python -m mypy .`
- `black .` or `ruff format .`

#### Testing Conventions

**Note**: Tests MUST be run via composer scripts when available:
- `composer test` - runs PEST tests via scripts/test.sh (auto-detects Sail)
- `composer e2e` - runs E2E tests which executes `bun run test:e2e`

[Modify these commands to match the actual codebase conventions]

When generating acceptance criteria, check the project's `composer.json` or `package.json` for the actual test script names and use those consistently.

### Step 9: Output the JSON File

Write the complete JSON object to `tasks/prd.json`:

```json
{
  "project": "TaskFlow",
  "branchName": "feature/task-status",
  "description": "Task Status Feature - Track task progress with status indicators",
  "userStories": [
    {
      "id": "US-001",
      "title": "...",
      "description": "...",
      "acceptanceCriteria": ["..."],
      "priority": 1,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-001-02",
      "title": "Agent-Browser Verification: ...",
      "description": "...",
      "acceptanceCriteria": ["..."],
      "priority": 1.1,
      "passes": false,
      "notes": ""
    }
  ]
}
```

---

## Splitting Large PRDs

If a PRD has big features, split them into smaller stories:

**Original:**

> "Add user notification system"

**Split into:**

1. US-001: Add notifications table to database
2. US-002: Create notification service for sending notifications
3. US-003: Add notification bell icon to header
4. US-004: Create notification dropdown panel
5. US-005: Add mark-as-read functionality
6. US-006: Add notification preferences page

Each is one focused change that can be completed and verified independently.

---

## Verification Decision Matrix

| Story Type | Example | PEST Test | -02 Browser | -03 E2E |
|------------|---------|-----------|-------------|---------|
| UI Component | "Create user profile page" | ❌ | ✅ | ❌ |
| UI + Workflow | "User registration form with validation" | ❌ | ✅ | ✅ |
| API Endpoint | "POST /api/users returns user data" | ✅ | ❌ | ❌ |
| API + UI Consumer | "Display user list from API" | ✅ | ✅ | ❌ |
| CRUD Feature | "Admin can create/edit/delete products" | ✅ | ✅ | ✅ |
| Database Migration | "Add email_verified column to users" | ✅ | ❌ | ❌ |
| Background Job | "Send welcome email after registration" | ✅ | ❌ | ❌ |
| Auth Flow | "User login with remember me" | ✅ | ✅ | ✅ |
| Config/Setup | "Add environment variables for SMTP" | ❌ | ❌ | ❌ |
| Styling Only | "Update button colors to match brand" | ❌ | ✅ | ❌ |

---

## Validation Checklist

Before saving prd.json, verify:

- [ ] **Previous prd.json archived** (if exists with different branchName)
- [ ] Each story is completable in one session (small enough)
- [ ] Stories are ordered by dependency (schema → backend → UI)
- [ ] Backend stories have PEST test criteria
- [ ] UI stories have "Verify in browser using agent-browser skill" criterion
- [ ] UI stories have corresponding -02 (browser verification) story
- [ ] Complex UI stories have corresponding -03 (E2E test) story
- [ ] Every story has code quality check criteria (lint/format/typecheck)
- [ ] Acceptance criteria are verifiable (not vague)
- [ ] No story depends on a later story
- [ ] Project name and branchName are correctly derived
- [ ] branchName is kebab-case with `feature/` prefix
- [ ] JSON is valid and properly formatted

---

## Example Transformation

### Input (from PRD):

```markdown
# Task Status Feature

Add ability to mark tasks with different statuses.

## Tech Stack
- Frontend: Vue 3 with TypeScript
- Backend: Laravel

## Functional Requirements

- FR-1: Add status field to tasks table with values: pending, in_progress, done
- FR-2: Display status badge on each task card
- FR-3: Allow toggling status from the task list
- FR-4: Filter task list by status
```

### Output (prd.json):

```json
{
  "project": "TaskApp",
  "branchName": "feature/task-status",
  "description": "Task Status Feature - Track task progress with status indicators",
  "userStories": [
    {
      "id": "US-001",
      "title": "Add status field to tasks table",
      "description": "As a developer, I need to store task status in the database.",
      "acceptanceCriteria": [
        "Add status column: 'pending' | 'in_progress' | 'done' (default 'pending')",
        "Generate and run migration successfully",
        "Write PEST tests for Task model status attribute",
        "Run 'composer lint' to format code",
        "Run 'composer test' to verify tests pass"
      ],
      "priority": 1,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-002",
      "title": "Display status badge on task cards",
      "description": "As a user, I want to see task status at a glance.",
      "acceptanceCriteria": [
        "Take 'before' screenshot: 'agent-browser screenshot tasks/screenshots/US-002-before.png'",
        "Each task card shows colored status badge",
        "Badge colors: gray=pending, blue=in_progress, green=done",
        "Match mockup in tasks/mockups/task-list.html",
        "Use colors from tasks/design-system/tokens.json",
        "Run 'bun format' to format frontend code",
        "Run 'bun run typecheck' to verify types",
        "Verify in browser using agent-browser skill",
        "If visual issues found: adjust and re-verify until correct",
        "Take 'after' screenshot: 'agent-browser screenshot tasks/screenshots/US-002-after.png'"
      ],
      "priority": 2,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-002-02",
      "title": "Agent-Browser Verification: Display status badge on task cards",
      "description": "Verify the status badge display using agent-browser CLI tool for visual confirmation.",
      "acceptanceCriteria": [
        "Navigate using 'agent-browser open <app-url>/tasks'",
        "Take snapshot with 'agent-browser snapshot -i' to verify badges",
        "Verify badge colors visually (gray=pending, blue=in_progress, green=done)",
        "Take screenshot with 'agent-browser screenshot tasks/screenshots/status-badges.png'",
        "Document verification in notes with screenshot path"
      ],
      "priority": 2.1,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-003",
      "title": "Add status toggle to task list rows",
      "description": "As a user, I want to change task status directly from the list.",
      "acceptanceCriteria": [
        "Take 'before' screenshot: 'agent-browser screenshot tasks/screenshots/US-003-before.png'",
        "Each row has status dropdown or toggle",
        "Changing status calls PATCH /api/tasks/{id} endpoint",
        "Write PEST test for status update endpoint",
        "UI updates without page refresh",
        "Run 'composer lint' to format PHP code",
        "Run 'bun format' to format frontend code",
        "Run 'bun run typecheck' to verify types",
        "Run 'composer test' to verify tests pass",
        "Verify in browser using agent-browser skill",
        "If visual issues found: adjust and re-verify until correct",
        "Take 'after' screenshot: 'agent-browser screenshot tasks/screenshots/US-003-after.png'"
      ],
      "priority": 3,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-003-02",
      "title": "Agent-Browser Verification: Add status toggle to task list rows",
      "description": "Verify status toggle functionality using agent-browser CLI tool for visual confirmation.",
      "acceptanceCriteria": [
        "Navigate using 'agent-browser open <app-url>/tasks'",
        "Take snapshot with 'agent-browser snapshot -i' to verify toggle elements",
        "Verify status dropdown/toggle is visible on each row",
        "Take screenshot with 'agent-browser screenshot tasks/screenshots/status-toggle.png'",
        "Document verification in notes with screenshot path"
      ],
      "priority": 3.1,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-003-03",
      "title": "E2E Test: Add status toggle to task list rows",
      "description": "Create an e2e test to verify status toggle works correctly end-to-end.",
      "acceptanceCriteria": [
        "Create a Playwright e2e test in tests/e2e/<feature>-<task>.spec.ts",
        "Test navigates to task list page",
        "Test changes a task status via the toggle",
        "Test verifies status change persists after page reload",
        "Build assets before testing with 'bun run build'",
        "Test must pass when run with 'composer e2e'"
      ],
      "priority": 3.2,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-004",
      "title": "Filter tasks by status",
      "description": "As a user, I want to filter the list to see only certain statuses.",
      "acceptanceCriteria": [
        "Take 'before' screenshot: 'agent-browser screenshot tasks/screenshots/US-004-before.png'",
        "Filter dropdown: All | Pending | In Progress | Done",
        "Filter persists in URL params",
        "Write PEST test for filtered task list endpoint",
        "Run 'composer lint' to format PHP code",
        "Run 'bun format' to format frontend code",
        "Run 'bun run typecheck' to verify types",
        "Run 'composer test' to verify tests pass",
        "Verify in browser using agent-browser skill",
        "If visual issues found: adjust and re-verify until correct",
        "Take 'after' screenshot: 'agent-browser screenshot tasks/screenshots/US-004-after.png'"
      ],
      "priority": 4,
      "passes": false,
      "notes": ""
    },
    {
      "id": "US-004-02",
      "title": "Agent-Browser Verification: Filter tasks by status",
      "description": "Verify status filter functionality using agent-browser CLI tool for visual confirmation.",
      "acceptanceCriteria": [
        "Navigate using 'agent-browser open <app-url>/tasks'",
        "Take snapshot with 'agent-browser snapshot -i' to verify filter dropdown",
        "Verify filter options are visible: All, Pending, In Progress, Done",
        "Take screenshot with 'agent-browser screenshot tasks/screenshots/status-filter.png'",
        "Document verification in notes with screenshot path"
      ],
      "priority": 4.1,
      "passes": false,
      "notes": ""
    }
  ]
}
```

---

## Summary Output

After generating `prd.json`, provide a summary:

```
✅ Created tasks/prd.json

Project: TaskApp
Branch: feature/task-status

Design System: ✅ Found (mockups will be referenced)
  - tasks/design-system/tokens.json
  - tasks/mockups/task-list.html, tasks/mockups/task-detail.html

Summary:
- Total stories: 9
- Setup stories (US-000): 0
- Main stories: 4
- Browser verification stories (-02): 3
- E2E test stories (-03): 1

Stories by type:
- Backend/Database: 1
- UI components: 3
- Multi-step workflows: 1

Ready for Ralph Wiggum loop: ./scripts/ralph/ralph.sh
```

If no design system found:

```
Design System: ⚠️ Not found
  Consider running 'create design system' for better UI consistency
```

If archiving occurred:

```
📦 Archived previous prd.json to tasks/archive/2025-01-25-feature-user-auth/
```

If US-000 was added:

```
ℹ️  Note: US-000 (Testing Infrastructure Setup) was added because scripts/test.sh
   or composer.json scripts were missing. Ralph will set these up first.
```

---

## Error Handling

If the PRD is missing critical sections:

1. **No Functional Requirements** — Cannot convert. Report: "PRD has no Functional Requirements section. Please add numbered requirements (FR-1, FR-2, etc.) or clear feature descriptions."

2. **No Tech Stack** — Can still convert, but warn: "No Tech Stack section found. Using generic test commands. Consider adding tech stack for more accurate acceptance criteria."

3. **Ambiguous Requirements** — Convert with best effort, add note in the story's `notes` field: "Original requirement was ambiguous. Review acceptance criteria."

4. **Stories too large** — Flag in output: "Warning: US-003 may be too large for one session. Consider splitting."

---

## Important Rules

1. **All stories start with `passes: false`** — Ralph marks them true as it completes them
2. **Never skip verification analysis** — Always reason through whether each story needs PEST tests, -02, and/or -03
3. **Priority must maintain dependency order** — A story that depends on another must have higher priority number
4. **Acceptance criteria must be autonomous** — An AI agent should be able to verify completion without asking questions
5. **Include test commands** — Every story should have verifiable test/lint/format commands in acceptance criteria
6. **Check for testing infrastructure** — If `scripts/test.sh` or composer scripts are missing, add US-000 setup story
7. **Story size matters** — If a story can't be completed in one focused session, split it
8. **Archive before overwriting** — Always check for existing prd.json with different branchName
9. **UI stories require before/after screenshots** — Capture state before and after implementation in `tasks/screenshots/`
10. **Verification loop for UI** — Iterate (implement → verify → adjust) until visual verification passes
11. **Context limit at 70%** — If context reaches 70% capacity, STOP. Log challenges to `tasks/progress.txt`. Do NOT mark as passed.

---

## Prerequisite Check: Testing Infrastructure

Before generating user stories, check if the project has the required testing infrastructure:

1. Check if `scripts/test.sh` exists
2. Check if `composer.json` has the `test` and `e2e` scripts

**If either is missing**, add US-000 as the first story:

```json
{
  "id": "US-000",
  "title": "Project Setup: Testing Infrastructure",
  "description": "Set up testing scripts and composer commands required for the Ralph autonomous loop.",
  "acceptanceCriteria": [
    "Create scripts/test.sh with content from Reference Files section below",
    "Make scripts/test.sh executable: chmod +x scripts/test.sh",
    "Add required scripts to composer.json scripts section (see Reference Files section)",
    "Verify 'composer test' runs successfully (or reports no tests found)",
    "Verify 'composer e2e' runs successfully (or skips gracefully if no e2e tests exist)"
  ],
  "priority": 0,
  "passes": false,
  "notes": "Setup story - must complete before other stories. See Reference Files section in ralph-prd-to-json skill for exact file contents."
}
```

**If both exist**, skip US-000 and start with US-001.

---

## Reference Files

These are the exact file contents to use when creating the testing infrastructure.

### scripts/test.sh

```bash
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
  run_artisan --bail
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
```

### composer.json scripts (add to existing scripts section)

```json
{
  "scripts": {
    "test": "scripts/test.sh",
    "e2e": "npm run test:e2e --",
    "e2e-headed": "npm run test:e2e:headed --",
    "e2e-ui": "npm run test:e2e:ui --",
    "lint": "./vendor/bin/rector process && vendor/bin/pint && vendor/bin/phpcbf",
    "lint-check": "./vendor/bin/rector process --dry-run --ansi && vendor/bin/pint --test && vendor/bin/phpcs"
  }
}
```

**Note:** Merge these scripts into the existing `composer.json` scripts section. Do not replace the entire scripts section — only add the missing entries.

---

## Integration with Other Skills

### Works Great With:

**ralph-prd-generator:**
- Create PRD with ralph-prd-generator
- Convert to JSON with ralph-prd-to-json
- Complete workflow!

**agent-browser:**
- JSON includes agent-browser verification for UI stories
- Ensures visual confirmation

### Example Workflow

```
# Step 1: Create PRD
"Create a PRD for user authentication"
→ Saved to tasks/prd-user-authentication.md

# Step 2: Convert to JSON
"Convert tasks/prd-user-authentication.md to prd.json"
→ Saved to tasks/prd.json

# Step 3: Execute Ralph Loop
./scripts/ralph/ralph.sh 25
→ Ralph iterates through stories

# Step 4: Track Progress
→ Ralph updates passes: true and adds notes as stories complete
```
