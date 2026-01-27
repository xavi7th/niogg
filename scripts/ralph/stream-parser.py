#!/usr/bin/env python3
import sys, json

def print_tool_input(tool_name, tool_input):
    """Format and print tool inputs in a readable way"""
    if tool_name == "Bash":
        command = tool_input.get("command", "")
        print(f"⏺ Bash({command})", flush=True)
    elif tool_name == "Read":
        file_path = tool_input.get("file_path", "")
        print(f"⏺ Read({file_path})", flush=True)
    elif tool_name == "Edit" or tool_name == "Write":
        file_path = tool_input.get("file_path", "")
        print(f"⏺ {tool_name}({file_path})", flush=True)
    elif tool_name == "WebSearch":
        query = tool_input.get("query", "")
        print(f"⏺ WebSearch({query})", flush=True)
    elif tool_name == "WebFetch":
        url = tool_input.get("url", "")
        print(f"⏺ WebFetch({url})", flush=True)
    else:
        # Generic tool display
        params = ", ".join(f"{k}={v}" for k, v in tool_input.items() if v)
        if params:
            print(f"⏺ {tool_name}({params})", flush=True)
        else:
            print(f"⏺ {tool_name}(...)", flush=True)

pending_tool_name = None

for line in sys.stdin:
    line = line.strip()
    if not line:
        continue
    try:
        data = json.loads(line)

        # Handle stream events
        if data.get("type") == "stream_event":
            event = data.get("event", {})

            # Content block start
            if event.get("type") == "content_block_start":
                block_type = event.get("content_block", {}).get("type")
                if block_type == "thinking":
                    print("\n🧠 THINKING:", flush=True)
                elif block_type == "text":
                    print("\n📝 RESPONSE:", flush=True)
                elif block_type == "tool_use":
                    # Store tool name, wait for input in delta
                    pending_tool_name = event.get("content_block", {}).get("name", "unknown")

            # Content block delta (streaming text and tool inputs)
            elif event.get("type") == "content_block_delta":
                delta = event.get("delta", {})

                # Handle text streaming
                text = delta.get("text", "")
                if text:
                    print(text, end="", flush=True)

                # Handle tool input (comes as partial_json, but we need complete)
                # We'll wait for the assistant message instead

            # Content block stop - tool input should be complete by now
            elif event.get("type") == "content_block_stop":
                # Reset pending tool
                pending_tool_name = None

            # Message delta (completion)
            elif event.get("type") == "message_delta":
                stop_reason = event.get("delta", {}).get("stop_reason")
                if stop_reason:
                    print(f"\n✅ Done ({stop_reason})\n", flush=True)

        # Handle complete assistant messages (has full tool info)
        elif data.get("type") == "assistant":
            message = data.get("message", {})
            content = message.get("content", [])

            for block in content:
                if block.get("type") == "tool_use":
                    tool_name = block.get("name", "unknown")
                    tool_input = block.get("input", {})
                    print(f"\n🔧 ", end="", flush=True)
                    print_tool_input(tool_name, tool_input)

        # Handle tool results (the output from tool execution)
        elif data.get("type") == "tool_result":
            content = data.get("content", [])

            # Print tool output with indentation
            for item in content:
                if item.get("type") == "text":
                    output_text = item.get("text", "")
                    # Format output with tree-like indentation
                    lines = output_text.strip().split('\n')
                    for i, line in enumerate(lines):
                        if line.strip():
                            if i == 0:
                                print(f"  ⎿  {line}", flush=True)
                            else:
                                print(f"     {line}", flush=True)
                elif item.get("type") == "image":
                    print(f"  ⎿  [Image output]", flush=True)

    except json.JSONDecodeError as e:
        print(f"\n⚠️  JSON parse error: {e}", file=sys.stderr, flush=True)
    except Exception as e:
        print(f"\n⚠️  Error: {e}", file=sys.stderr, flush=True)



# USAGE INSTRUCTIONS
# chmod +x scripts/ralph/stream-parser.py
# ```

# ## What this adds:

# ### 1. **Detailed Tool Display**
# Shows the actual command/parameter being used:
# ```
# 🔧 ⏺ Bash(sleep 5 && agent-browser eval "...")
# 🔧 ⏺ Read(/path/to/file.json)
# 🔧 ⏺ WebSearch(email validation API)
# ```

# ### 2. **Tool Output Display**
# Shows the results with tree-like formatting:
# ```
#   ⎿  "Not found, waiting..."
#   ⎿  ✓ Browser closed
#   ⎿  ✓ Sign In | SmartCoop
# ```

# ### 3. **Error Handling**
# Catches JSON parse errors and shows them without crashing:
# ```
# ⚠️  JSON parse error: Expecting value: line 1 column 1 (char 0)
# ```

# ### 4. **Completion Indicator**
# ```
# ✅ Done (end_turn)
# ✅ Done (tool_use)


# Usage in your script:

# claude -p "$(cat "$PROTOCOL_FILE")" \
#   --dangerously-skip-permissions \
#   --no-session-persistence \
#   --print \
#   --output-format=stream-json \
#   --include-partial-messages | \
#   python3 -u "$PROJECT_ROOT/scripts/ralph/stream-parser.py"
