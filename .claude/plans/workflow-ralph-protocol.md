# Ralph Autonomous Loop Protocol

Definitive protocol for the Ralph agent autonomous loop. Processes `tasks/prd.json` user stories, enforcing quality, testing, and atomicity standards.

## Critical: Story Grouping Pattern

**For UI-related features/fixes, stories MUST be grouped in sets of 3:**

- **US-XXX-01**: Implementation (the actual code changes)
- **US-XXX-02**: agent-browser Verification (visual confirmation)
- **US-XXX-03**: E2E Test (automated test)

**CRITICAL**: ALL THREE stories in a group MUST be implemented and committed together in a SINGLE commit. This ensures:

- Tests are always available for code they verify
- Atomic, self-contained changes in git history
- Easier code review and rollback if needed

**Implementation order within single session:**

1. US-XXX-01 → Code changes
2. US-XXX-02 → Visual verification (BEFORE E2E test)
3. US-XXX-03 → E2E test (based on observations from US-XXX-02)

---

## Single Iteration Workflow

After each invocation, follow these steps:

### Step 1: Read Context

Read these files first (in order):

- **`tasks/prd.json`** - Understand current project state and all user stories
- **`tasks/progress.txt`** - Read the **Codebase Patterns** section at the TOP for critical learnings from previous iterations

The Codebase Patterns section consolidates reusable knowledge you must know before implementing.

### Step 2: Identify THE NEXT Story or Story Group

**CRITICAL: Implement ONLY ONE story or story group per iteration.**

- Find the **highest priority** user story where `passes: false`
- **If story is US-XXX-01**: You MUST implement US-XXX-01, US-XXX-02, and US-XXX-03 together as a single group
- **If story is standalone** (no -02/-03 variants): Implement ONLY this one story
- **If story is US-XXX-02 or US-XXX-03**: Complete the remaining stories in that group only
- **If ALL stories have `passes: true`**: Reply with `<promise>COMPLETE</promise>` and exit

**After identifying the story/group:**
- Implement ONLY this story or story group
- Do NOT continue to the next story after completion
- The iteration ends after Step 7

### Step 3: Implement Complete Story Group

Implement in this exact order:

#### **US-XXX-01: Implementation**

- Make code changes per acceptance criteria
- Follow project patterns found in progress.txt
- Focus on correctness and consistency
- Keep changes focused and minimal
- No commits yet

#### **US-XXX-02: agent-browser Verification**

- **CRITICAL**: Before any browser testing, run: `sail shell -c "bun run build"`
  - This builds assets INSIDE the Docker container
  - Local builds will NOT reflect in the browser
- Use the `agent-browser` skill to verify the implementation:
  - Navigate to the relevant page/section
  - Interact with the feature per acceptance criteria
  - Take screenshots as evidence of working implementation
  - Document selectors, element IDs, structure for E2E test
- Verify ALL acceptance criteria visually
- Do NOT write E2E test yet - observations from visual verification inform E2E selector choices

#### **US-XXX-03: E2E Test** (if applicable)

- Write E2E test based on actual structure observed during US-XXX-02
- Use selectors and elements discovered during visual verification
- Follow project's E2E testing patterns
- Run: `composer e2e <test-file>` to verify test works

### Step 4: Run Quality Checks

All checks must pass before proceeding to commit. Run in order:

```bash
# JavaScript/Svelte linting and formatting
bun run format

# PHP linting
composer lint

# Backend tests
composer test

# E2E tests (if created)
composer e2e <test-file>
```

Do NOT proceed if any checks fail. Fix issues and re-run checks until all pass.

### Step 5: Commit Atomically

Create ONE single commit containing:

- Code changes (US-XXX-01)
- Screenshots/verification evidence (US-XXX-02)
- E2E test file (US-XXX-03) if created

**Commit message format:**

- Use conventional commits: `feat: add dropdown filter`, `fix: resolve sidebar positioning`
- Describe the FEATURE/FIX, not the stories
- **DO NOT** include User Story IDs (no `US-005-01:` prefix)
- Example good messages:
  - `feat: add featured video to event showcase`
  - `fix: resolve hero image loading on mobile`
  - `refactor: consolidate video player logic`

**Files to exclude from commit:**

- Nothing in `tasks/` directory (not even screenshots - keep separately for documentation)
- Keep `tasks/progress.txt` and `tasks/prd.json` out of git (update them but don't commit)

### Step 6: Update Records

**Update `tasks/prd.json`:**

- Set `passes: true` for ALL THREE stories in the group
- Add detailed notes to each story (optional but recommended):
  - Implementation notes
  - Known issues or gotchas
  - Link to verification screenshots if kept
  - Example: `"notes": "Implemented hero video with fade animation. Desktop/tablet show featured; mobile shows featured-only with 'See More'. See /tasks/progress.txt for details."`

**Append to `tasks/progress.txt`:**

```
## [YYYY-MM-DD HH:MM] - US-XXX (Story Group)
- Implemented: [1-2 sentence description of what was built]
- Files changed: [list of modified files, e.g., Modules/PublicPage/app/Models/EventVideo.php, Modules/PublicPage/resources/js/Pages/EventShowcase.svelte, tests/Feature/EventVideoE2ETest.php]
- Learnings: [Critical patterns/gotchas for future iterations - these go into Codebase Patterns section if general enough]
---
```

**Update CLAUDE.md files** (if applicable):

- Check if you discovered reusable patterns in edited files' directories
- Look for existing CLAUDE.md in that directory or parents
- Add learnings that FUTURE iterations should know
- Example: "When modifying EventVideo model, also update EventVideoTransformer to keep serialization in sync"

### Step 7: End Iteration

**After updating records, the iteration MUST end.**

- **If there are still stories with `passes: false`**:
  - Reply normally (no special signal)
  - Next iteration will pick up the next `passes: false` story
  - **Do NOT continue to implement more stories**

- **If ALL stories now have `passes: true`**:
  - Reply with: `<promise>COMPLETE</promise>`
  - Do NOT add anything after the completion signal

**One story/group per iteration - this is mandatory.**

---

## Project Standards & Patterns

Follow these standards (detailed in project CLAUDE.md):

**Code Style:**

- Import sorting: by length (shortest first)
- Use `str_obfuscate()` helper for sensitive IDs in views
- Modular architecture: code lives in Modules/, not /app directory
- Model relationships: use standard Laravel Eloquent patterns

**Commit Format:**

- Conventional commits (feat:, fix:, refactor:, test:, docs:, chore:)
- Concise messages (under 50 characters when possible)
- Focus on "why" not "what"

**Testing:**

- All UI changes require E2E test (US-XXX-03)
- Backend changes should have feature/unit tests
- Never commit failing tests

**Browser Verification:**

- ALWAYS run `sail shell -c "bun run build"` before browser testing
- Use agent-browser skill, not manual browser (for consistency)
- Take screenshots as evidence in progress.txt

---

## Workflow Loop Execution (for ralph.sh)

The `scripts/ralph/ralph.sh` script orchestrates this protocol:

1. **Initialize**: Check for `tasks/progress.txt` (create if missing)
2. **Loop** (up to MAX_ITERATIONS):
   - Pass this protocol file to Claude as prompt
   - Claude follows the workflow above
   - Check if all stories have `passes: true`
   - If yes, exit with success
   - If no, continue to next iteration

**Run with:**

```bash
./scripts/ralph/ralph.sh 10  # Max 10 iterations
```

---

## Stop Conditions

### Success (Completion)

All user stories have `passes: true`. Agent replies: `<promise>COMPLETE</promise>`

### Iteration End (Continue)

- Story group implemented successfully
- All quality checks pass
- Records updated
- Agent replies normally (no completion signal)
- Next iteration picks up next `passes: false` story

### Failure State (Do Not Commit)

- Quality check failed (lint, test, build error)
- Fix the issue and re-run checks
- Do NOT commit broken code

---

## Important Rules Summary

✅ **DO:**

- Work on complete story groups (US-XXX-01/02/03 together)
- Run asset build before browser verification
- Use actual selectors from visual verification for E2E tests
- Commit atomically (single commit for three stories)
- Update both prd.json and progress.txt
- Follow conventional commit format
- Run quality checks before committing

❌ **DO NOT:**

- Commit individual stories from a group separately
- Forget to build assets before browser testing
- Skip quality checks
- Write E2E tests without visual verification first
- Include User Story IDs in commit messages
- Commit files in tasks/ directory
- Skip verification for UI changes

**ITERATION SCOPE:**

- **ONE story or story group per iteration** - this is the most critical rule
- Standalone stories (US-001, US-002, etc.) = one iteration each
- Story groups (US-XXX-01/02/03) = one iteration for all three together
- Do NOT implement multiple stories/groups in a single iteration
- After Step 7, the iteration ends - period

---

## Examples

### Example: Implementing US-005 Story Group (Events Gallery Filter)

**US-005-01:** Add event category filter to gallery

- Modify EventGallery.svelte component
- Add dropdown filter logic
- No screenshots, no tests yet

**US-005-02:** Visual verification

- Build: `sail shell -c "bun run build"`
- Navigate to events gallery page
- Click dropdown, select categories
- Take screenshot showing working filter
- Document dropdown selector (e.g., `[data-testid="category-filter"]`)

**US-005-03:** E2E test

```javascript
// Based on selectors from US-005-02
test("filter events by category", async ({ page }) => {
  await page.goto("/events");
  await page.click('[data-testid="category-filter"]');
  await page.selectOption("select", "charity");
  await expect(page.locator('[data-testid="event-item"]')).toHaveCount(3);
});
```

**Single commit:**

```
feat: add event category filter to gallery

- Modified: Modules/PublicPage/resources/js/Pages/EventGallery.svelte
- Added E2E test: tests/Feature/EventFilterE2ETest.php
- Screenshots in progress.txt
```

---

## Questions & Clarifications

If unclear on any acceptance criteria:

- Re-read the story requirements in prd.json
- Check progress.txt Codebase Patterns for related learnings
- Follow existing code patterns in the project
- Implement conservatively (follow existing style, don't innovate)

---

**Last Updated:** 2026-01-23
**Status:** Active - Use this for all Ralph iterations
