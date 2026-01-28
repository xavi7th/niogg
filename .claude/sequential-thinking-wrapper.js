#!/usr/bin/env node

/**
 * Sequential Thinking Wrapper - Handles type coercion for Claude Sonnet 4.5
 *
 * Problem: Claude sometimes sends string numbers ("1") instead of integers (1)
 * Solution: Intercept and coerce types before passing to the MCP server
 */

import { spawn } from "node:child_process";
import { createInterface } from "node:readline";

console.error("[Sequential-Thinking Wrapper] Starting with type coercion...");

// Start the actual sequential-thinking server
const serverProcess = spawn("bunx", ["-y", "@modelcontextprotocol/server-sequential-thinking@latest"], {
  stdio: ["pipe", "pipe", "inherit"],
  shell: true,
});

// Create readline interfaces for line-by-line processing
const inputReader = createInterface({
  input: process.stdin,
  crlfDelay: Infinity,
});

const outputReader = createInterface({
  input: serverProcess.stdout,
  crlfDelay: Infinity,
});

/**
 * Coerce string numbers to actual numbers in the data
 */
function coerceTypes(data) {
  try {
    const parsed = JSON.parse(data);

    // Check if this is a tool call with parameters
    if (parsed.params && parsed.params.arguments) {
      const args = parsed.params.arguments;

      // Coerce numeric parameters
      const numericFields = ["thoughtNumber", "totalThoughts", "revisesThought", "branchFromThought"];

      numericFields.forEach((field) => {
        if (args[field] !== undefined) {
          // Convert string numbers to actual numbers
          if (typeof args[field] === "string" && !isNaN(args[field])) {
            args[field] = parseInt(args[field], 10);
            console.error(`[Wrapper] Coerced ${field}: "${args[field]}" -> ${args[field]}`);
          }
        }
      });

      // Coerce boolean parameters
      const booleanFields = ["nextThoughtNeeded", "isRevision", "needsMoreThoughts"];

      booleanFields.forEach((field) => {
        if (args[field] !== undefined) {
          if (typeof args[field] === "string") {
            args[field] = args[field].toLowerCase() === "true";
            console.error(`[Wrapper] Coerced ${field}: "${args[field]}" -> ${args[field]}`);
          }
        }
      });
    }

    return JSON.stringify(parsed);
  } catch (e) {
    // If not JSON or parsing fails, return as-is
    return data;
  }
}

// Forward stdin to server with type coercion
inputReader.on("line", (line) => {
  const coerced = coerceTypes(line);
  serverProcess.stdin.write(coerced + "\n");
});

// Forward server output to stdout
outputReader.on("line", (line) => {
  process.stdout.write(line + "\n");
});

// Handle process termination
process.stdin.on("end", () => {
  serverProcess.stdin.end();
});

serverProcess.on("exit", (code, signal) => {
  console.error(`[Wrapper] Server exited with code ${code}, signal ${signal}`);
  process.exit(code || 0);
});

serverProcess.on("error", (error) => {
  console.error(`[Wrapper] Server error:`, error);
  process.exit(1);
});

// Forward signals
process.on("SIGTERM", () => serverProcess.kill("SIGTERM"));
process.on("SIGINT", () => serverProcess.kill("SIGINT"));
