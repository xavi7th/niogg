#!/bin/bash
# Ralph Wiggum - Autonomous Agent Loop
# Processes prd.json user stories per workflow-ralph-protocol.md

set -e

MAX_ITERATIONS=${1:-10}
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
PRD_FILE="$PROJECT_ROOT/tasks/prd.json"
PROGRESS_FILE="$PROJECT_ROOT/tasks/progress.txt"
PROTOCOL_FILE="$PROJECT_ROOT/scripts/ralph/prompt.md"

echo "Starting Ralph - Max iterations: $MAX_ITERATIONS"
echo "Project root: $PROJECT_ROOT"

if [ ! -d "$PROJECT_ROOT/.git" ]; then
  echo "❌ Git repository not found at $PROJECT_ROOT"
  exit 1
fi

if [ ! -f "$PRD_FILE" ]; then
  echo "❌ PRD file not found at $PRD_FILE"
  exit 1
fi

if ! command -v jq >/dev/null 2>&1; then
  echo "❌ 'jq' is required to read branchName from $PRD_FILE"
  exit 1
fi

BRANCH_NAME=$(jq -r '.branchName // empty' "$PRD_FILE")

if [ -z "$BRANCH_NAME" ]; then
  echo "❌ 'branchName' missing in $PRD_FILE"
  exit 1
fi

CURRENT_BRANCH=$(git -C "$PROJECT_ROOT" rev-parse --abbrev-ref HEAD)

if [ "$CURRENT_BRANCH" = "$BRANCH_NAME" ]; then
  echo "On branch '$CURRENT_BRANCH' (matches prd.json)."
else
  echo "Switching to branch '$BRANCH_NAME' (current: '$CURRENT_BRANCH')."
  git -C "$PROJECT_ROOT" checkout -B "$BRANCH_NAME"
fi

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

  export ANTHROPIC_BASE_URL="https://api.z.ai/api/anthropic"
  export ANTHROPIC_AUTH_TOKEN="0f9f828a6d084a86843e1d750d64687c.zGbkaDV5yW08yFgR"
  export API_TIMEOUT_MS="3000000"
  export ANTHROPIC_DEFAULT_SONNET_MODEL="GLM-4.7"
  export ANTHROPIC_DEFAULT_OPUS_MODEL="GLM-4.7"
  export ANTHROPIC_DEFAULT_HAIKU_MODEL="GLM-4.5-AIR"

  if command -v claude &> /dev/null; then
    # Run Claude with protocol as prompt
    claude -p "$(cat "$PROTOCOL_FILE")" --dangerously-skip-permissions --no-session-persistence --print --output-format=stream-json --include-partial-messages | python3 -u "$PROJECT_ROOT/scripts/ralph/stream-parser.py"
      # jq -r --unbuffered '
      #   if .type == "stream_event" and .event.type == "message_start" then
      #     "🚀 Starting...\n"
      #   elif .type == "stream_event" and .event.type == "content_block_start" and .event.content_block.type == "text" then
      #     "\n📝 RESPONSE:\n"
      #   elif .type == "stream_event" and .event.type == "content_block_start" and .event.content_block.type == "tool_use" then
      #     "\n🔧 TOOL: " + .event.content_block.name + "\n"
      #   elif .type == "stream_event" and .event.type == "content_block_delta" and .event.delta.type == "text_delta" then
      #     .event.delta.text
      #   elif .type == "stream_event" and .event.type == "message_delta" and .event.delta.stop_reason then
      #     "\n✅ Done (" + .event.delta.stop_reason + ")\n"
      #   else
      #     empty
      #   end
      # '

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
