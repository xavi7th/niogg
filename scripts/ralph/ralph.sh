#!/bin/bash
# Ralph Wiggum - Autonomous Agent Loop
# Processes prd.json user stories per workflow-ralph-protocol.md

set -e

MAX_ITERATIONS=${1:-10}
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
PRD_FILE="$PROJECT_ROOT/tasks/prd.json"
PROGRESS_FILE="$PROJECT_ROOT/tasks/progress.txt"
PROTOCOL_FILE="$PROJECT_ROOT/.claude/plans/workflow-ralph-protocol.md"

echo "Starting Ralph - Max iterations: $MAX_ITERATIONS"
echo "Project root: $PROJECT_ROOT"

# Initialize progress.txt if missing
if [ ! -f "$PROGRESS_FILE" ]; then
  mkdir -p "$PROJECT_ROOT/tasks"
  echo "# Ralph Progress Log" > "$PROGRESS_FILE"
  echo "Started: $(date)" >> "$PROGRESS_FILE"
  echo "---" >> "$PROGRESS_FILE"
fi

for i in $(seq 1 $MAX_ITERATIONS); do
  echo ""
  echo "═══════════════════════════════════════════════════════"
  echo "  Ralph Iteration $i of $MAX_ITERATIONS"
  echo "═══════════════════════════════════════════════════════"

  if command -v claude &> /dev/null; then
    # Run Claude with protocol as prompt
    claude -p "$(cat "$PROTOCOL_FILE")" --dangerously-skip-permissions
  else
    echo "❌ 'claude' CLI not found."
    echo ""
    echo "If using Antigravity (Google AI Studio):"
    echo "  Do not run this script directly."
    echo "  Instead, ask: 'Execute the Ralph loop as described in scripts/ralph/README.md'"
    echo ""
    echo "If using VSCode with Claude Code:"
    echo "  Ensure 'claude' CLI is installed: npm install -g @anthropic-ai/claude-code"
    exit 1
  fi

  # Check completion (all stories pass)
  if grep -q '"passes": false' "$PRD_FILE"; then
    echo ""
    echo "Iteration $i complete. Continuing..."
  else
    echo ""
    echo "✓ Ralph completed all tasks!"
    echo "Completed at iteration $i of $MAX_ITERATIONS"
    exit 0
  fi
done

echo ""
echo "⚠ Ralph reached max iterations ($MAX_ITERATIONS) without completing all tasks."
echo "Check $PROGRESS_FILE for status."
exit 1
