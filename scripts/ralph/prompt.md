# Ralph Autonomous Loop Protocol

---

## Quick Reference

```
┌─────────────────────────────────────────────────────────────────┐
│  RALPH ITERATION CHECKLIST                                      │
├─────────────────────────────────────────────────────────────────┤
│  1. READ    tasks/progress.txt (check Codebase Patterns)        │
│  2. READ    tasks/prd.json (find next passes: false)            │
│  3. SELECT  ONE story or story group (01/02/03)                 │
│  4. EXECUTE Implementation → Verification → Tests               │
│  5. QUALITY composer lint, bun format, composer test            │
│  6. UPDATE  prd.json (passes: true) + progress.txt              │
│  7. COMMIT  git commit (do NOT commit tasks/ folder)            │
│  8. STOP    Do not start next story                             │
└─────────────────────────────────────────────────────────────────┘

START COMMANDS:
make start_dev

QUALITY_COMMANDS:
composer lint           # Run Pint + PHPCBF
sail shell -c "bun run build"  # Build frontend assets
sail test               # Run all tests

AGENT-BROWSER COMMANDS:
agent-browser open <url>                    # Navigate
agent-browser snapshot -i -c                # Get interactive elements (compact)
agent-browser click @e1                     # Click by ref
agent-browser fill @e2 "text"               # Fill input
agent-browser screenshot path.png           # Viewport screenshot
agent-browser screenshot --full path.png    # Full page screenshot
agent-browser scrollintoview @e1            # Scroll element into view
agent-browser wait --load networkidle       # Wait for page load

CONTEXT LIMIT: Stop at 70% context. Log to progress.txt. Do NOT mark as passed.

COMPLETION: When ALL stories pass → <promise>COMPLETE</promise>
```

---

## 1. Story Grouping Strategy (The "Trinity")

**For UI-related features, stories are processed in groups of 3:**
1. **US-XXX-01**: Implementation (Code)
2. **US-XXX-02**: Visual Verification (Screenshots)
3. **US-XXX-03**: E2E Test (Playwright/Code)

**CRITICAL:** All three MUST be implemented and committed in a SINGLE atomic commit.

---

## 2. Iteration Workflow

### Step 1: Load Context

1. Read **`tasks/progress.txt`** FIRST
   - Check **Codebase Patterns** section to avoid repeating past mistakes
   - Note any challenges logged by previous iterations
2. Read **`tasks/prd.json`** to identify the backlog

### Step 2: Select Target

**CRITICAL: Implement ONLY ONE story or story group per iteration.**

- Find the **highest priority** story/group where `"passes": false`
- **If story is a group (has -02/-03 variants):** Execute the ENTIRE group in this iteration
- **If story is standalone:** Implement ONLY this one story
- If all stories are `true`, output `<promise>COMPLETE</promise>` and stop

### Step 3: Execution

#### A. Before Screenshot (UI Stories Only)

For any story involving UI changes:
```bash
agent-browser open "<app-url>/relevant-page"
agent-browser screenshot tasks/screenshots/US-XXX-before.png
```

#### B. Implement (US-XXX-01)

- Write the code. Keep changes minimal and focused.
- **Do not commit yet.**

#### C. Verify (US-XXX-02) — UI Stories

1. **Build first** (required for frontend changes):
   ```bash
   sail shell -c "bun run build"   # Docker
   ```

2. **Navigate and inspect:**
   ```bash
   agent-browser open "<app-url>/relevant-page"
   agent-browser snapshot -i -c    # Get interactive elements, compact
   ```

3. **Take screenshots:**
   ```bash
   # Viewport screenshot (for specific component/section)
   agent-browser screenshot tasks/screenshots/US-XXX-after.png

   # Full page screenshot (for entire page layout)
   agent-browser screenshot --full tasks/screenshots/US-XXX-full.png

   # Scroll to specific element first if needed
   agent-browser scrollintoview @e5
   agent-browser screenshot tasks/screenshots/US-XXX-component.png
   ```

4. **Verification loop:**
   - If visual issues found → adjust code → rebuild → re-verify
   - Repeat until visual verification passes
   - Do NOT proceed to tests until UI is correct

#### D. Test (US-XXX-03) — Smart Testing Strategy

**Do not blindly create new test files.**

1. **Search**: Look for existing test file for this feature
2. **Extend**: If found, add a new test case to it
3. **Create**: Only create new file if brand new feature area
4. **Skip**: Purely cosmetic changes (CSS color, typo) don't need new tests, but run existing suite

Write E2E tests based on actual DOM structure observed during verification:
```bash
agent-browser snapshot -i -c    # Get refs for selectors
```

#### E. Test Interactive Elements

```bash
# Test form interactions
agent-browser fill @e2 "test@example.com"
agent-browser click @e3
agent-browser wait --load networkidle

# Verify result
agent-browser snapshot -i -c
agent-browser screenshot tasks/screenshots/US-XXX-result.png
```

### Step 4: Quality Gate

Run these checks. If ANY fail, fix immediately.

```bash
composer lint           # Format PHP code
sail shell -c "bun run build"  # Build frontend
sail test               # Run tests
```

**FAILURE PROTOCOL:** If checks fail after 3 attempts:
1. Do NOT commit
2. Append failure log to `tasks/progress.txt`
3. Output: "ABORTING: Unable to pass quality checks for US-XXX."
4. Stop execution

### Step 5: Context Limit Check

**CRITICAL: Monitor your context usage.**

If context reaches **70% capacity**:
1. **STOP immediately** — do not continue the story
2. Log to `tasks/progress.txt`:
   ```
   ## [YYYY-MM-DD HH:MM] - US-XXX (CONTEXT LIMIT REACHED)
   - Status: Incomplete - stopped at 70% context
   - Progress so far: [describe what was completed]
   - Remaining work: [describe what still needs to be done]
   - Challenges: [any issues encountered]
   - Files modified (uncommitted): [list files]
   ---
   ```
3. Do NOT mark the story as passed
4. Do NOT commit partial work
5. Stop execution — next iteration will resume

### Step 6: Update Records

Only proceed if Step 4 passed AND context is under 70%.

**Update `tasks/prd.json`:**
- Set `passes: true` for completed stories
- Add notes (optional but recommended)

**Append to `tasks/progress.txt`:**
```
## [YYYY-MM-DD HH:MM] - US-XXX (Story Group)
- Implemented: [1-2 sentence description]
- Files changed: [list of modified files]
- Screenshots: tasks/screenshots/US-XXX-before.png, US-XXX-after.png
- Learnings: [patterns/gotchas for future iterations]
---
```

### Step 7: Learnings Documentation

**Update CLAUDE.md files** where applicable:
- Check for reusable patterns in edited directories
- Add learnings FUTURE iterations should know
- Do NOT add generic information

### Step 8: Atomic Commit

```bash
git add [implementation_files] [test_files]
git commit -m "feat: [description of feature/fix]"
```

**Rules:**
- Use conventional commits: `feat:`, `fix:`, `refactor:`
- Do NOT `git add .`
- Do NOT commit files inside `tasks/`

### Step 9: Handover

- Provide brief summary of what was done
- **Do NOT start the next story**
- Stop execution

**If ALL stories now have `passes: true`:**
```
<promise>COMPLETE</promise>
```
Do NOT add anything after the completion signal.

---

## 3. Before/After Screenshot Requirements

**ALL UI stories must capture:**

| Screenshot | When | Naming |
|------------|------|--------|
| Before | Start of UI story | `tasks/screenshots/US-XXX-before.png` |
| After | End of implementation | `tasks/screenshots/US-XXX-after.png` |
| Full page (optional) | When layout matters | `tasks/screenshots/US-XXX-full.png` |
| Component (optional) | Specific element focus | `tasks/screenshots/US-XXX-component.png` |

This enables human verification of changes.

---

## 4. Verification Loop for UI Stories

```
┌──────────────┐
│  Implement   │
└──────┬───────┘
       ▼
┌──────────────┐
│    Build     │
└──────┬───────┘
       ▼
┌──────────────┐     Issues found
│   Verify     │─────────────────┐
└──────┬───────┘                 │
       │ OK                      ▼
       ▼                  ┌──────────────┐
┌──────────────┐          │    Adjust    │
│    Tests     │          └──────┬───────┘
└──────┬───────┘                 │
       │                         │
       ▼                         │
┌──────────────┐                 │
│   Commit     │◄────────────────┘
└──────────────┘
```

**Do NOT proceed to tests until visual verification passes.**

---

## 5. Codebase Patterns & Rules

Follow ALL standards in project CLAUDE.md. Key rules:

* **Modular Architecture:** All admin code goes in `Modules/PublicPage/`
* **Laravel 10:** Use Artisan commands, Eloquent, Form Requests, Policies
* **Frontend:** Svelte with Inertia.js, Tailwind CSS
* **Testing:**
  - ALWAYS build before browser testing
  - Never commit without browser verification for UI changes
  - All backend changes MUST have feature/unit tests
  - Save screenshots in `tasks/screenshots/`
* **Context:** Stop at 70% — log challenges, don't mark passed

---

## 6. Project-Specific Context

**Architecture:** Modular Monolith with Laravel 10 + Svelte
**Module:** PublicPage
**Admin Routes:** `Modules/PublicPage/routes/web.php`
**Admin Controllers:** `Modules/PublicPage/app/Http/Controllers/Admin/`
**Admin Pages:** `Modules/PublicPage/resources/js/Pages/Admin/`

**Key Constraints:**
- Max file size: 1 GB per video upload
- No soft deletes (permanent deletion only)
- Role-based access: Admin/Super Admin only
- Bulk uploads with chunked uploads and resume capability
- Automatic thumbnail generation with Intervention Image
- Redis caching for performance

**Existing Models:**
- Event: id, name, description, icon, category, event_date, slug, is_published
- Video: id, event_id, title, description, video_url, thumbnail_url, duration_seconds, is_featured, sort_order

---

## 7. Stop Conditions

### Success (Completion)
All stories have `passes: true`. Output: `<promise>COMPLETE</promise>`

### Iteration End (Continue)
- Story completed successfully
- Quality checks pass
- Records updated
- Committed
- Next iteration picks up next story

### Context Limit (Pause)
- Context reached 70%
- Logged to progress.txt
- NOT marked as passed
- NOT committed
- Next iteration resumes

### Failure (Abort)
- Quality checks failed 3 times
- Logged to progress.txt
- Output: "ABORTING: Unable to pass quality checks for US-XXX."
- Stop execution
