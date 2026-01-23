# Ralph for Antigravity

This document provides instructions for Antigravity agents to emulate the "Ralph" autonomous loop.

## How to Execute

If a user asks you to "Execute the Ralph loop" or "Run Ralph", follow these steps:

1.  **Adopt the Persona**: You are an autonomous coding agent working on the project.
2.  **Follow the Loop Logic**:

    **Step 1: Read Context**
    - Read `tasks/prd.json` to understand the current project state and user stories.
    - Read `tasks/progress.txt` to see what has been done and learn from previous iterations.

    **Step 2: Pick a Story Group**
    - Identify the **highest priority** user story in `prd.json` where `passes: false`.
    - **CRITICAL**: If the story is **US-XXX-01** (implementation), you must also implement **US-XXX-02** (E2E test) and **US-XXX-03** (dev-browser verification) in the same session and commit them together.
    - If the story is **US-XXX-02** or **US-XXX-03**, this indicates incomplete work. Complete the remaining stories in the group.
    - If ALL stories are `passes: true`, stop and inform the user that all tasks are complete.

    **Step 3: Implement the Complete Story Group**
    - For UI-related features/fixes, implement all three stories together in this order:
      - **US-XXX-01**: The actual code changes (fix/feature implementation)
      - **US-XXX-03**: Dev-browser visual verification FIRST (helps inform E2E test selectors)
      - **US-XXX-02**: E2E test for the fix/feature (based on observations from dev-browser)
    - Follow the acceptance criteria strictly for each story.
    - Run necessary quality checks (typecheck, lint, test).
    - **Crucial**: For UI stories, do dev-browser verification BEFORE writing E2E tests.

    **Step 4: Update Records and Commit Atomically**
    - Update `tasks/prd.json`: Set `passes: true` for **all three stories in the group** (US-XXX-01, US-XXX-03, US-XXX-02).
    - Append to `tasks/progress.txt`: Log your work for the entire story group, files changed, and any learnings.
    - Create a **SINGLE atomic commit** containing:
      - The code changes (US-XXX-01)
      - Any verification artifacts/screenshots (US-XXX-03)
      - The E2E test file (US-XXX-02)
    - Use conventional commit format (e.g., `feat: add ebook category dropdown`, `fix: resolve dropdown interaction`).
    - **Do NOT** include User Story IDs in commit messages.

    **Step 5: Repeat**
    - After completing one story, check if there are more `passes: false` stories.
    - If yes, **continue immediately** to the next story (loop back to Step 2).
    - If no, stop and report completion.

## Story Grouping Pattern

**CRITICAL**: For UI-related features/fixes, stories are grouped in sets of 3:

- **US-XXX-01**: Implementation (the actual fix/feature)
- **US-XXX-03**: Dev-Browser Verification (visual confirmation - DO THIS FIRST)
- **US-XXX-02**: E2E Test (automated test - informed by dev-browser observations)

**Implementation Order:** US-XXX-01 → US-XXX-03 → US-XXX-02

**Why dev-browser first?**

- Faster feedback on whether the fix works
- Visual inspection reveals actual UI structure and selectors
- Informs more accurate E2E test implementation
- Reduces time spent debugging blind E2E test failures

**All three stories MUST be implemented and committed together in a SINGLE atomic commit.** This ensures:

- Tests are always available for the code they verify
- Atomic, self-contained changes in git history
- Easier code review and rollback if needed

## Important Rules

- **Story Groups, Not Individual Stories**: For UI changes, implement all three stories (US-XXX-01, US-XXX-02, US-XXX-03) together in one session.
- **Single Atomic Commit**: Commit the implementation, E2E test, and verification artifacts together.
- **Verify**: Never mark a story as passed without verification (especially browser verification for UI).
- **Commit Messages**: Use standard conventional commits (e.g., `feat: add user login`, `fix: resolve null error`). Do NOT include the User Story ID in the commit message.
- **Learn**: Use the `progress.txt` file to pass knowledge to your future self (or the next agent).

## Example Prompts

Here are some ways users might ask you to run this loop:

### Planning & Setup

- "Generate a PRD for [feature name]. I want to convert it to JSON later for the Ralph loop."
- "Convert the PRD at tasks/prd-[feature].md into prd.json for the Ralph loop."

### Execution

- "Execute the Ralph loop."
- "Run Ralph to work on the current PRD."
- "Act as Ralph and implement the next user story from prd.json."
- "Please run the autonomous loop as described in scripts/ralph/README.md."
