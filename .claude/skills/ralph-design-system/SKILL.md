---
name: ralph-design-system
description: "Generate a design system and HTML mockups for PRD features. Analyzes existing project styles, reference URLs, and uploaded images to create consistent design tokens and page mockups. Use before converting PRD to JSON. Triggers on: create design system, generate mockups, design the UI, design system for, mockup for, design this feature, UI design for PRD."
color: yellowgreen
---

# Design System & Mockup Generator for Ralph Wiggum

You are a UI/UX designer helping create a consistent design system and visual mockups before development begins. Your goal is to bridge the gap between PRD requirements and implementation by providing concrete visual references.

---

## Overview

This skill:
1. Analyzes existing project design patterns (if any)
2. Gathers design inspiration from user (URLs, images, preferences)
3. Generates a design system (tokens, components)
4. Creates HTML/CSS mockups for each page/feature in the PRD
5. Outputs files that the JSON converter can reference in stories

---

## Output Structure

```
tasks/
├── design-system/
│   ├── tokens.json              # Design tokens (colors, typography, spacing)
│   ├── tailwind.extend.js       # Tailwind config extensions (if using Tailwind)
│   ├── components.html          # Component library preview
│   └── README.md                # Design system documentation
├── mockups/
│   ├── [page-name].html         # Full HTML/CSS mockup for each page
│   ├── [page-name]-mobile.html  # Mobile variant (if needed)
│   └── README.md                # Mockup index and notes
└── prd-[feature].md
```

---

## CRITICAL: Always Ask Discovery Questions

**NEVER skip the discovery phase.** Even if the user provides detailed requirements:
- Always check for existing design systems first
- Always ask about design preferences and references
- Always confirm understanding before generating

---

## Phase 1: Discovery

### Step 1: Analyze Existing Project Design

Before asking questions, scan the project for existing design patterns:

**Check for Tailwind CSS:**
```bash
# Look for tailwind config
cat tailwind.config.js 2>/dev/null || cat tailwind.config.ts 2>/dev/null
```

**Check for CSS variables:**
```bash
# Look for CSS custom properties
grep -r "--color\|--font\|--spacing" resources/css/ src/styles/ 2>/dev/null | head -20
```

**Check for existing component library:**
```bash
# Look for UI components
ls -la resources/js/Components/ src/components/ components/ 2>/dev/null
```

**Check for design tokens:**
```bash
# Look for existing token files
find . -name "tokens.json" -o -name "theme.json" -o -name "design-system*" 2>/dev/null
```

### Step 2: Ask Discovery Questions

Ask these questions **one at a time**:

**1. Design References:**
```
Do you have any design references I should use as inspiration?

A. Website URL(s) I like the design of
B. Uploaded images/screenshots of designs I like
C. Existing design system or brand guidelines
D. No references - create something modern and clean
```

**2. If user provides URL(s):**
- Use `web_fetch` to retrieve the page
- Analyze: color palette, typography, spacing, layout patterns, component styles
- Extract key design decisions

**3. If user provides images:**
- Analyze the uploaded images
- Extract: dominant colors, layout structure, typography style, UI patterns
- Note specific elements the user might want to replicate

**4. Style Preferences:**
```
What style direction fits your project?

A. Minimal / Clean (lots of whitespace, simple)
B. Bold / Vibrant (strong colors, dynamic)
C. Corporate / Professional (traditional, trustworthy)
D. Playful / Friendly (rounded, colorful, fun)
E. Dark mode / Technical (dark backgrounds, modern)
F. Match the reference I provided
```

**5. Color Preferences:**
```
Any specific brand colors I should use?

A. Yes - [ask them to provide hex codes]
B. Extract from the reference I provided
C. No - suggest a palette based on style preference
```

**6. Typography Preferences:**
```
Font preferences?

A. System fonts (fast, no loading)
B. Google Fonts - [ask which ones]
C. Match the reference I provided
D. Suggest based on style preference
```

**7. Component Complexity:**
```
What level of component detail do you need in mockups?

A. Wireframe level (layout and structure only)
B. Low fidelity (basic styling, placeholder content)
C. High fidelity (production-ready HTML/CSS)
```

---

## Phase 2: Analyze References

### Analyzing URLs

When user provides a reference URL:

```bash
# Fetch the page
web_fetch <url>
```

**Extract and document:**
- **Colors:** Primary, secondary, accent, background, text colors
- **Typography:** Font families, sizes, weights, line heights
- **Spacing:** Padding/margin patterns, grid structure
- **Components:** Button styles, form inputs, cards, navigation
- **Layout:** Header, sidebar, content area patterns
- **Interactions:** Hover states, transitions, shadows

### Analyzing Images

When user provides reference images:

**Document observations:**
- Color palette (extract 5-8 key colors)
- Layout structure (grid, sidebar, single column)
- Typography style (serif, sans-serif, display)
- Component patterns (rounded vs sharp, shadows vs flat)
- Overall mood/feeling

---

## Phase 3: Generate Design System

### Step 1: Create tokens.json

```json
{
  "colors": {
    "primary": {
      "50": "#eff6ff",
      "100": "#dbeafe",
      "500": "#3b82f6",
      "600": "#2563eb",
      "700": "#1d4ed8"
    },
    "secondary": {
      "50": "#f8fafc",
      "500": "#64748b",
      "700": "#334155"
    },
    "accent": {
      "500": "#10b981"
    },
    "background": {
      "primary": "#ffffff",
      "secondary": "#f8fafc",
      "tertiary": "#f1f5f9"
    },
    "text": {
      "primary": "#0f172a",
      "secondary": "#475569",
      "muted": "#94a3b8",
      "inverse": "#ffffff"
    },
    "border": {
      "default": "#e2e8f0",
      "strong": "#cbd5e1"
    },
    "status": {
      "success": "#10b981",
      "warning": "#f59e0b",
      "error": "#ef4444",
      "info": "#3b82f6"
    }
  },
  "typography": {
    "fontFamily": {
      "sans": "Inter, system-ui, sans-serif",
      "mono": "JetBrains Mono, monospace"
    },
    "fontSize": {
      "xs": "0.75rem",
      "sm": "0.875rem",
      "base": "1rem",
      "lg": "1.125rem",
      "xl": "1.25rem",
      "2xl": "1.5rem",
      "3xl": "1.875rem",
      "4xl": "2.25rem"
    },
    "fontWeight": {
      "normal": "400",
      "medium": "500",
      "semibold": "600",
      "bold": "700"
    },
    "lineHeight": {
      "tight": "1.25",
      "normal": "1.5",
      "relaxed": "1.75"
    }
  },
  "spacing": {
    "0": "0",
    "1": "0.25rem",
    "2": "0.5rem",
    "3": "0.75rem",
    "4": "1rem",
    "5": "1.25rem",
    "6": "1.5rem",
    "8": "2rem",
    "10": "2.5rem",
    "12": "3rem",
    "16": "4rem",
    "20": "5rem"
  },
  "borderRadius": {
    "none": "0",
    "sm": "0.125rem",
    "default": "0.25rem",
    "md": "0.375rem",
    "lg": "0.5rem",
    "xl": "0.75rem",
    "2xl": "1rem",
    "full": "9999px"
  },
  "shadows": {
    "sm": "0 1px 2px 0 rgb(0 0 0 / 0.05)",
    "default": "0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)",
    "md": "0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)",
    "lg": "0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)",
    "xl": "0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)"
  }
}
```

### Step 2: Create tailwind.extend.js (if using Tailwind)

```javascript
// Paste into tailwind.config.js theme.extend
module.exports = {
  colors: {
    primary: {
      50: '#eff6ff',
      100: '#dbeafe',
      500: '#3b82f6',
      600: '#2563eb',
      700: '#1d4ed8',
    },
    // ... rest of colors from tokens
  },
  fontFamily: {
    sans: ['Inter', 'system-ui', 'sans-serif'],
    mono: ['JetBrains Mono', 'monospace'],
  },
  // ... rest of extensions
}
```

### Step 3: Create components.html

Generate an HTML file showcasing all components:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Design System - Component Library</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>/* Custom styles and token overrides */</style>
</head>
<body class="bg-gray-50 p-8">
  <h1 class="text-3xl font-bold mb-8">Component Library</h1>

  <!-- Colors -->
  <section class="mb-12">
    <h2 class="text-xl font-semibold mb-4">Colors</h2>
    <div class="flex gap-4">
      <div class="w-20 h-20 bg-primary-500 rounded"></div>
      <!-- ... more color swatches -->
    </div>
  </section>

  <!-- Typography -->
  <section class="mb-12">
    <h2 class="text-xl font-semibold mb-4">Typography</h2>
    <p class="text-4xl font-bold">Heading 1</p>
    <p class="text-3xl font-bold">Heading 2</p>
    <!-- ... more typography examples -->
  </section>

  <!-- Buttons -->
  <section class="mb-12">
    <h2 class="text-xl font-semibold mb-4">Buttons</h2>
    <div class="flex gap-4">
      <button class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">Primary</button>
      <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Secondary</button>
      <button class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Outline</button>
    </div>
  </section>

  <!-- Form Inputs -->
  <section class="mb-12">
    <h2 class="text-xl font-semibold mb-4">Form Inputs</h2>
    <div class="space-y-4 max-w-md">
      <input type="text" placeholder="Text input" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-primary-500">
      <select class="w-full px-3 py-2 border rounded">
        <option>Select option</option>
      </select>
      <textarea placeholder="Textarea" class="w-full px-3 py-2 border rounded"></textarea>
    </div>
  </section>

  <!-- Cards -->
  <section class="mb-12">
    <h2 class="text-xl font-semibold mb-4">Cards</h2>
    <div class="bg-white p-6 rounded-lg shadow max-w-sm">
      <h3 class="font-semibold mb-2">Card Title</h3>
      <p class="text-gray-600">Card content goes here.</p>
    </div>
  </section>

  <!-- ... more components -->
</body>
</html>
```

---

## Phase 4: Generate Page Mockups

### Step 1: Identify Pages from PRD

Read the PRD and identify all pages/views that need mockups:
- Login/Register pages
- Dashboard
- Settings
- Feature-specific pages
- Modals/dialogs

### Step 2: Generate HTML Mockups

For each page, create a complete HTML file:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>[Page Name] - Mockup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          // Paste token extensions here
        }
      }
    }
  </script>
  <style>
    /* Any custom styles */
  </style>
</head>
<body>
  <!-- Full page mockup with realistic content -->
</body>
</html>
```

**Mockup requirements:**
- Use design tokens consistently
- Include realistic placeholder content (not "Lorem ipsum" everywhere)
- Show all states where relevant (empty, loading, error, populated)
- Include responsive considerations
- Add comments for developer guidance: `<!-- Component: UserCard -->`

### Step 3: Iteration Loop

After generating mockups, ask for feedback:

```
I've created mockups for [pages]. Please review:

1. tasks/mockups/login.html
2. tasks/mockups/dashboard.html
3. tasks/mockups/settings.html

Open these in your browser and let me know:
- What's working well?
- What needs adjustment? (colors, spacing, layout, components)
- Any missing elements?

I'll iterate until you're happy with the designs.
```

**Iterate until user approves:**
- Make specific adjustments based on feedback
- Re-generate affected mockups
- Show before/after if helpful

---

## Phase 5: Handoff

### Create Design System README

```markdown
# Design System

## Quick Start

1. Design tokens: `tasks/design-system/tokens.json`
2. Tailwind extensions: `tasks/design-system/tailwind.extend.js`
3. Component preview: Open `tasks/design-system/components.html` in browser

## Colors

| Name | Usage | Value |
|------|-------|-------|
| primary-500 | Primary actions, links | #3b82f6 |
| ... | ... | ... |

## Typography

- Headings: Inter, semibold/bold
- Body: Inter, regular
- Code: JetBrains Mono

## Component Patterns

See `components.html` for live examples of:
- Buttons (primary, secondary, outline, ghost)
- Form inputs (text, select, checkbox, radio)
- Cards
- Navigation
- Modals
```

### Create Mockups README

```markdown
# Page Mockups

## Index

| Page | File | Description |
|------|------|-------------|
| Login | login.html | User authentication page |
| Dashboard | dashboard.html | Main application dashboard |
| Settings | settings.html | User settings and preferences |

## Usage

Open any `.html` file in a browser to preview.

These mockups serve as the visual reference for implementation.
Stories in prd.json will reference these files.

## Design Decisions

- [Document key decisions made during design]
- [Note any compromises or alternatives considered]
```

### Summary Output

```
✅ Design System Complete

Created:
- tasks/design-system/tokens.json (design tokens)
- tasks/design-system/tailwind.extend.js (Tailwind config)
- tasks/design-system/components.html (component library)
- tasks/design-system/README.md (documentation)

Mockups:
- tasks/mockups/login.html
- tasks/mockups/dashboard.html
- tasks/mockups/settings.html
- tasks/mockups/README.md

Next step: Convert PRD to JSON with "convert prd to json"
The converter will reference these mockups in story acceptance criteria.
```

---

## Integration with JSON Converter

After this skill completes, the JSON converter should:

1. Check for `tasks/design-system/` and `tasks/mockups/`
2. Reference mockups in acceptance criteria:
   ```json
   {
     "acceptanceCriteria": [
       "Match mockup in tasks/mockups/login.html",
       "Use colors from tasks/design-system/tokens.json",
       "Follow component patterns in tasks/design-system/components.html"
     ]
   }
   ```

---

## Important Rules

1. **Always scan for existing design system first** — Don't override established patterns
2. **Always ask about references** — User input improves output quality significantly
3. **Generate real HTML, not descriptions** — Mockups must be viewable in browser
4. **Use Tailwind CDN for mockups** — Makes them self-contained and portable
5. **Iterate on feedback** — Don't finalize until user approves
6. **Document decisions** — Future developers need context

---

## Sequential Thinking Tool

**If sequential_thinking MCP server is available, USE IT for:**
- Analyzing reference designs to extract patterns
- Planning component hierarchy
- Determining color palette relationships
- Structuring page layouts

---

## Example Workflow

```
User: "Create design system for my email app PRD"

Skill: [Scans project, finds Tailwind]
       "I found Tailwind CSS in your project. I'll extend your existing config.

        Do you have any design references I should use as inspiration?
        A. Website URL(s)
        B. Uploaded images
        C. Existing brand guidelines
        D. No - create something modern"

User: "A - check out superhuman.com"

Skill: [Fetches superhuman.com, analyzes design]
       "Great reference! Superhuman uses:
        - Minimal, dark-mode aesthetic
        - Purple accent color
        - System fonts for speed
        - Generous whitespace

        Should I follow this direction?
        A. Yes, match this closely
        B. Use as loose inspiration
        C. Take only the [specific element]"

User: "A"

Skill: [Generates tokens.json, components.html, page mockups]
       [Asks for feedback, iterates]
       [Finalizes and saves all files]
```
