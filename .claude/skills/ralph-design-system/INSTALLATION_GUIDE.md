# Installation Guide: Ralph Design System Skill

This guide walks you through installing the Ralph Design System skill into your Claude Code project.

---

## Prerequisites

Before installing this skill, ensure you have:

1. **Claude Code CLI** installed and configured
2. A PRD document in `tasks/prd-*.md` (or you're about to create one)
3. Optional: Reference images or URLs for design inspiration

---

## Installation Steps

### Step 1: Create the Skills Directory

If you don't already have a skills directory in your project, create one:

```bash
mkdir -p .claude/skills
```

### Step 2: Copy the Skill File

Copy `ralph-design-system-skill.md` into your skills directory:

```bash
cp ralph-design-system-skill.md .claude/skills/ralph-design-system/SKILL.md
```

### Step 3: Create Output Directories

The skill will create these automatically, but you can set them up in advance:

```bash
mkdir -p tasks/design-system
mkdir -p tasks/mockups
```

---

## Project Structure After Installation

Your project should look like this:

```
your-project/
├── .claude/
│   ├── settings.json
│   └── skills/
│       └── ralph-design-system/
│           └── SKILL.md    <-- The design system skill
├── tasks/
│   ├── design-system/
│   │   ├── tokens.json                      <-- Design tokens
│   │   ├── tailwind.extend.js               <-- Tailwind config (if using)
│   │   ├── components.html                  <-- Component library
│   │   └── README.md
│   ├── mockups/
│   │   ├── login.html                       <-- Page mockups
│   │   ├── dashboard.html
│   │   └── README.md
│   └── prd-[feature-name].md
└── ... (your project files)
```

---

## Usage

Once installed, you can trigger the skill by saying:

- "Create design system"
- "Generate mockups"
- "Design the UI"
- "Design system for this PRD"
- "Mockup for login page"
- "UI design for PRD"

### Example Prompts

**Create full design system:**

```
Create a design system for my email app PRD
```

**With reference URL:**

```
Create design system based on superhuman.com design
```

**With uploaded images:**

```
[Upload screenshot]
Create a design system inspired by this design
```

**Generate specific mockups:**

```
Generate mockups for the login and dashboard pages from my PRD
```

---

## Typical Workflow

1. **Create PRD first:**

   ```
   Create a PRD for user authentication
   ```

2. **Generate design system:**

   ```
   Create design system for this PRD
   ```

   - Provide reference URLs or images if you have them
   - Answer questions about style preferences
   - Review mockups and iterate

3. **Convert to JSON:**

   ```
   Convert PRD to JSON
   ```

   - Converter automatically references mockups in stories

4. **Run Ralph loop:**

   ```bash
   ./scripts/ralph/ralph.sh 25
   ```

   - Ralph uses mockups as visual reference for implementation

---

## Output Files

### tasks/design-system/tokens.json

Design tokens in JSON format:

```json
{
  "colors": {
    "primary": { "500": "#3b82f6", ... },
    "text": { "primary": "#0f172a", ... }
  },
  "typography": { ... },
  "spacing": { ... },
  "borderRadius": { ... },
  "shadows": { ... }
}
```

### tasks/design-system/tailwind.extend.js

Tailwind config extensions (if using Tailwind):

```javascript
module.exports = {
  colors: { ... },
  fontFamily: { ... },
  // Paste into tailwind.config.js theme.extend
}
```

### tasks/design-system/components.html

Interactive component library preview. Open in browser to see:

- Color swatches
- Typography scale
- Buttons (all variants)
- Form inputs
- Cards
- Other components

### tasks/mockups/\*.html

Self-contained HTML mockups for each page. Features:

- Uses Tailwind CDN (no build required)
- Realistic placeholder content
- Responsive design
- Comments marking component boundaries

---

## Integration with Other Skills

### Recommended Workflow Order

```
1. ralph-prd-generator     → Create PRD
2. ralph-design-system     → Create design system + mockups
3. ralph-prd-to-json       → Convert to JSON (references mockups)
4. ralph.sh                → Execute stories
```

### How JSON Converter Uses Design System

When `tasks/design-system/` exists, the converter adds to UI story acceptance criteria:

```json
{
  "acceptanceCriteria": ["Match mockup in tasks/mockups/login.html", "Use colors from tasks/design-system/tokens.json", "Follow patterns in tasks/design-system/components.html"]
}
```

---

## Providing Design References

### Option 1: Website URLs

```
Create design system based on:
- linear.app (for the clean aesthetic)
- stripe.com/docs (for the documentation style)
```

The skill will fetch these pages and analyze:

- Color palette
- Typography
- Spacing patterns
- Component styles

### Option 2: Uploaded Images

Upload screenshots or design files directly in the chat. The skill will analyze:

- Dominant colors
- Layout structure
- Typography style
- UI patterns

### Option 3: Brand Guidelines

If you have existing brand colors or fonts:

```
Create design system using:
- Primary color: #6366f1
- Font: Inter
- Style: minimal and clean
```

---

## Troubleshooting

### Skill Not Triggering

1. Verify skill file is in `.claude/skills/`
2. Check frontmatter is correct
3. Restart Claude Code session

### Mockups Not Rendering

1. Open HTML files directly in browser (not through IDE preview)
2. Check browser console for errors
3. Ensure Tailwind CDN is accessible

### Design System Not Found by Converter

1. Verify files are in `tasks/design-system/`
2. Check `tokens.json` exists and is valid JSON
3. Run design system skill before converter

---

## Uninstallation

To remove the skill:

```bash
rm .claude/skills/ralph-design-system/SKILL.md
```

This only removes the skill. Generated design systems and mockups remain in `tasks/`.
