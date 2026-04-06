# Gap Analysis Skill Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create a Claude Code skill (`/gap-analysis <PR-number>`) that analyzes a GitHub PR and produces a gap analysis report showing what Frontman tools/capabilities are missing to build that feature itself.

**Architecture:** Single SKILL.md file with embedded tool inventory, three-agent sequential pipeline (PR extraction → operation classification → gap analysis), and a markdown report template. The skill orchestrates by dispatching subagents via the Agent tool, passing context as structured text between them.

**Tech Stack:** Claude Code skill (markdown), `gh` CLI for PR data, Agent tool for subagent dispatch.

---

## File Structure

| Action | Path | Responsibility |
|--------|------|----------------|
| Create | `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md` | Complete skill: frontmatter, orchestrator logic, agent prompts, tool inventory, report template |

Single file. No supporting files, scripts, or config needed.

---

### Task 1: Create Skill Directory and Frontmatter

**Files:**
- Create: `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

- [ ] **Step 1: Create the skill directory**

Run: `mkdir -p /home/bluehotdog/.claude/skills/gap-analysis`

- [ ] **Step 2: Write the frontmatter and skill header**

Write to `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`:

```markdown
---
name: gap-analysis
description: >-
  Analyze a GitHub PR to identify what tools, capabilities, and integrations
  Frontman is missing to have built that feature itself. Use when asked to
  assess Frontman's readiness for a PR, dogfooding gap analysis, or capability
  audit against real PRs. Invoked as /gap-analysis <PR-number>.
---

# Gap Analysis

Analyze a GitHub PR from the current repo and produce a gap analysis report
identifying what Frontman tools, capabilities, and integrations are missing
to have built that PR's feature itself.

**Input**: PR number (assumes current repo context)
**Output**: Markdown report to stdout with coverage breakdown and recommendations

## Prerequisites

- `gh` CLI must be installed and authenticated with access to the current repo
- Must be run from within a git repository
```

- [ ] **Step 3: Verify the file was created**

Run: `cat /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected: The frontmatter and header content above.

- [ ] **Step 4: Commit**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "feat: scaffold gap-analysis skill with frontmatter"
```

---

### Task 2: Add Input Validation and Orchestrator Flow

**Files:**
- Modify: `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

- [ ] **Step 1: Append the validation and orchestrator sections**

Append to `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`:

````markdown

## Step 1: Validate Input

The PR number is passed as the skill argument (e.g., `/gap-analysis 123`).

1. Extract the PR number from the argument string
2. Run: `gh pr view <number> --json number,title,state`
3. If the command fails, report the error to the user and **stop**:
   - "PR #<number> not found in this repo" (if 404)
   - "gh CLI not authenticated — run `gh auth login`" (if auth error)
4. If valid, announce: "Analyzing PR #<number>: <title>"

## Step 2: Orchestrate Three-Agent Pipeline

Run three subagents **sequentially** using the Agent tool. Each agent receives the output of the previous one as context in its prompt.

```
PR number → Agent 1 (extract) → Agent 2 (classify) → Agent 3 (analyze) → Report
```

**Important**: Each agent is dispatched via the Agent tool with `subagent_type: "general-purpose"`. Pass the full output of the previous agent as context in the next agent's prompt. Do NOT summarize or truncate — pass it verbatim.

**Large PR guard**: After Agent 1 returns, check the file count. If 500+ files changed, prepend this warning to the Agent 2 prompt: "WARNING: This is a large PR (N files). Group files by directory/concern and process in batches. Note in your output that precision may be reduced for individual operations."
````

- [ ] **Step 2: Verify the appended content**

Run: `tail -30 /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected: The validation and orchestrator sections.

- [ ] **Step 3: Commit**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "feat(gap-analysis): add input validation and orchestrator flow"
```

---

### Task 3: Add Agent 1 — PR Context Extractor Prompt

**Files:**
- Modify: `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

- [ ] **Step 1: Append the Agent 1 prompt section**

Append to `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`:

````markdown

## Agent 1: PR Context Extractor

Dispatch with description: "Extract PR context for gap analysis"

**Prompt to send to the agent:**

> You are extracting full context from a GitHub PR for a gap analysis. Your job is to gather ALL information needed to understand what operations were required to build this PR.
>
> **PR number**: {number}
>
> Run these commands and collect their output:
>
> 1. `gh pr view {number} --json title,body,labels,number,state,baseRefName,headRefName,files`
> 2. `gh pr diff {number}`
> 3. `gh pr view {number} --json commits --jq '.commits[].messageHeadline'`
> 4. `gh pr view {number} --json comments --jq '.comments[].body'`
>
> Then analyze the PR description and comments for referenced issues. For each referenced issue (e.g., "#456", "fixes #789"), run:
> - `gh issue view <issue-number> --json title,body`
>
> Finally, for each changed file, examine the surrounding context:
> - What directory does it live in? What other files are nearby?
> - Is it a config file (CI workflow, package.json, migration, Makefile)?
> - Does the change reference external systems (APIs, databases, CI, deployment)?
>
> **Produce a structured report with these sections:**
>
> ### PR Metadata
> - Title, number, state, base branch, head branch, labels
>
> ### PR Intent
> - 2-3 sentence summary of what this PR does and why (from description + issues)
>
> ### Files Changed
> - List every file with: path, change type (added/modified/deleted), category (source code, test, config, migration, docs, CI, assets, other)
>
> ### Commit Messages
> - All commit messages in order
>
> ### External Systems Referenced
> - CI/CD configs touched or referenced
> - Package manager changes (package.json, mix.exs, Cargo.toml, etc.)
> - Database migrations or schema changes
> - Deployment or infrastructure configs
> - External API integrations mentioned
> - Environment variable or secret references
>
> ### Raw Diff
> - Include the complete diff output
>
> Be thorough. Do not summarize the diff — include it in full.
````

- [ ] **Step 2: Verify**

Run: `grep -c "Agent 1" /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected: At least 2 matches (heading + content)

- [ ] **Step 3: Commit**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "feat(gap-analysis): add Agent 1 PR context extractor prompt"
```

---

### Task 4: Add Agent 2 — Operation Classifier Prompt

**Files:**
- Modify: `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

- [ ] **Step 1: Append the Agent 2 prompt section**

Append to `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`:

````markdown

## Agent 2: Operation Classifier

Dispatch with description: "Classify PR operations for gap analysis"

**Prompt to send to the agent:**

> You are classifying every discrete operation that was required to produce a GitHub PR. You receive the full PR context from the extractor agent. Your job is to identify EVERY action a developer (or AI agent) would need to perform to create this PR from scratch.
>
> Think step-by-step: what did the developer actually DO? Not just "edited files" — but specifically: read existing code to understand it, searched for usages, created a new file, modified a function signature, updated a test, ran the test suite, added a dependency, ran a migration, etc.
>
> **PR Context:**
> {paste Agent 1's full output here}
>
> **Classify each operation into one of these categories:**
>
> | Category | Examples |
> |----------|----------|
> | File I/O | read, write, edit, create, delete, rename, move files |
> | Code Understanding | search codebase, navigate structure, trace dependencies, read docs |
> | Browser/UI | visual inspection, DOM manipulation, interaction, responsive testing |
> | Testing | write tests, run test suite, interpret test results, fix failing tests |
> | Build/Compile | compilation, bundling, type checking, linting, formatting |
> | Source Control | branching, committing, rebasing, conflict resolution, PR creation |
> | Package Management | add/remove/update dependencies, lockfile changes |
> | Database | migrations, schema changes, seed data, query changes |
> | CI/CD | workflow edits, pipeline triggers, read pipeline logs, artifact management |
> | Infrastructure | deployment config, environment variables, secrets, server config |
> | External APIs | third-party service calls, webhook setup, OAuth flows |
> | Design/Assets | image creation/editing, SVG changes, font changes, icon updates |
> | Documentation | README, changelog, inline docs, API docs, comments |
>
> **For each operation, output:**
>
> ```
> - **Category**: [category name]
>   **Operation**: [what was done, in imperative form — e.g., "Edit the TaskChannel module to add a new handler"]
>   **Evidence**: [file path + brief description of the relevant change]
>   **Confidence**: [high/medium/low — high if the diff clearly shows this, low if inferred]
> ```
>
> **Important rules:**
> - Include implicit operations: if test files were modified, someone had to RUN those tests too. If a migration was added, someone had to RUN the migration. If dependencies changed, someone ran `npm install` or `mix deps.get`.
> - Include code understanding operations: before editing a file, the developer read it. Before adding a function call, they searched for the function. Before modifying a test, they ran it first to understand current behavior.
> - Do NOT merge operations. "Edited 5 files" is wrong — list each file edit separately.
> - Do NOT invent operations not supported by evidence. If you're unsure, mark confidence as "low".
>
> **Output format:**
> Start with a summary line: "N operations identified across M categories"
> Then list all operations grouped by category.
````

- [ ] **Step 2: Verify**

Run: `grep -c "Agent 2" /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected: At least 2 matches

- [ ] **Step 3: Commit**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "feat(gap-analysis): add Agent 2 operation classifier prompt"
```

---

### Task 5: Add Static Tool Inventory

**Files:**
- Modify: `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

- [ ] **Step 1: Append the tool inventory section**

Append to `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`:

````markdown

## Frontman Tool Inventory (Static Snapshot — April 2026)

This is the complete set of tools Frontman has available. Agent 3 uses this to determine coverage.

### File Operations
| Tool | Description |
|------|-------------|
| `read_file` | Read file contents with offset/limit pagination |
| `write_file` | Write file content or image_ref, auto-creates parent directories |
| `edit_file` | Find-and-replace with multi-strategy fuzzy matching (exact, trimmed, whitespace-normalized, indentation-flexible) |
| `file_exists` | Check if a file or directory exists |
| `search_files` | Find files by name pattern (glob-style) |
| `list_files` | List directory contents, respects .gitignore |
| `list_tree` | Recursive directory tree view with workspace detection |
| `grep` | Search file contents with regex/literal/glob patterns (ripgrep-based) |

### Browser Interaction
| Tool | Description |
|------|-------------|
| `take_screenshot` | Capture viewport or specific element as image, supports full-page |
| `get_dom` | Inspect DOM subtrees via CSS selector/XPath, simplified or full HTML mode, configurable depth/node limits |
| `get_interactive_elements` | List interactive elements (buttons, inputs, links) with ARIA roles and accessible names |
| `interact_with_element` | Click, hover, or focus elements via CSS selector, ARIA role, or visible text |
| `execute_js` | Run arbitrary JavaScript in preview iframe with console log capture, 30KB output cap |
| `search_text` | Find visible text on page (like Ctrl+F), returns elements with surrounding context |
| `set_device_mode` | Responsive device emulation with presets (iPhone, iPad, Pixel, laptop, 4K) |

### Human-in-the-Loop
| Tool | Description |
|------|-------------|
| `question` | Ask the user questions with predefined options, supports multiple choice + freetext, blocks agent until answered |

### Server/Utility
| Tool | Description |
|------|-------------|
| `web_fetch` | Fetch web pages and convert to markdown, line-based pagination, SSRF protection, 5MB limit |
| `todo_write` | Atomic todo list management (full list replacement per call) |
| `lighthouse` | Google Lighthouse audits for performance, accessibility, SEO, best practices |
| `load_agent_instructions` | Discover and load Agents.md/CLAUDE.md files by walking up directory tree |

### Framework-Specific
| Framework | Tools |
|-----------|-------|
| Next.js | `get_routes` (list app/pages routes), `get_logs` (dev server logs with filtering), `edit_file` override (checks webpack/turbopack errors after edit) |
| Astro | `get_client_pages` (list pages with dynamic route analysis), `get_logs` (build/console logs), `edit_file` override |
| Vite | `get_logs` (dev server logs), `edit_file` override |

### Extensibility
| Mechanism | Description |
|-----------|-------------|
| MCP servers | External tool providers can register tools via Model Context Protocol; Frontman routes calls to browser client |

### Known Limitations (No Current Tool)
| Capability | Status |
|------------|--------|
| Terminal/shell execution | Not available |
| Git operations (commit, branch, push, rebase) | Not available |
| Package management (npm install, yarn add, mix deps.get) | Not available |
| Database operations (migrations, queries, seeds) | Not available |
| CI/CD interaction (trigger builds, read pipeline logs) | Not available |
| Test execution (run suites, interpret results) | Not available |
| Environment/secret management | Not available |
| Code generation/scaffolding | Not available |
| Image/asset creation or manipulation | Not available |
| API client tools (REST/GraphQL beyond web_fetch) | Not available |
| Deployment operations | Not available |
````

- [ ] **Step 2: Verify the inventory is complete**

Run: `grep -c "Not available" /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected: 11 (one per known limitation)

- [ ] **Step 3: Commit**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "feat(gap-analysis): add static Frontman tool inventory snapshot"
```

---

### Task 6: Add Agent 3 — Gap Analyzer Prompt

**Files:**
- Modify: `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

- [ ] **Step 1: Append the Agent 3 prompt section**

Append to `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`:

````markdown

## Agent 3: Gap Analyzer

Dispatch with description: "Produce gap analysis report"

**Prompt to send to the agent:**

> You are producing a Frontman gap analysis report. You receive classified operations from a PR and a static tool inventory. Your job is to match each operation against Frontman's capabilities and produce a structured markdown report.
>
> **Classified Operations:**
> {paste Agent 2's full output here}
>
> **Frontman Tool Inventory:**
> {paste the tool inventory from the skill document above}
>
> **For each classified operation, determine its coverage status:**
>
> - **Covered**: The operation maps directly to an existing Frontman tool. Name the tool.
> - **Partially covered**: A tool exists but lacks a specific capability needed. Name the tool and describe the gap.
> - **Not covered**: No Frontman tool handles this operation.
> - **MCP-addressable**: Not natively covered, but could be solved by adding an MCP server. Suggest a server name and rationale.
>
> **Matching rules:**
> - File read/write/edit/search/list operations → File Operations tools
> - Visual inspection of a web page → Browser Interaction tools (take_screenshot, get_dom)
> - Clicking, typing, navigating in a browser → Browser Interaction tools (interact_with_element, execute_js)
> - Fetching external web content → web_fetch
> - Running tests, builds, linters → Not covered (no terminal execution)
> - Git operations → Not covered
> - Database migrations → Not covered
> - Package install/update → Not covered
> - CI/CD pipeline interaction → Not covered
> - Creating images/assets from scratch → Not covered
> - Asking the user a question → question tool (Covered)
> - Reading route structure → Framework-specific tools (Covered if matching framework)
> - Reading dev server logs → Framework-specific tools (Covered if matching framework)
>
> **For MCP-addressable gaps**, suggest realistic MCP servers:
> - `git-mcp` — git operations (commit, branch, push, diff, log)
> - `terminal-mcp` — shell command execution with sandboxing
> - `package-manager-mcp` — npm/yarn/mix dependency management
> - `database-mcp` — migration running, schema queries
> - `ci-mcp` — GitHub Actions trigger, status check, log reading
> - `test-runner-mcp` — test suite execution with result parsing
> - Only suggest servers that make architectural sense (stateless per call, well-defined inputs/outputs)
>
> **Produce the report in this exact format:**
>
> ```markdown
> # Gap Analysis: PR #<number> — <title>
>
> ## PR Summary
> <2-3 sentence description of what this PR accomplished and why>
>
> ## Operations Breakdown
> <total> operations identified across <N> categories
>
> ### Covered by Frontman (<count>)
> | Operation | Tool | Notes |
> |-----------|------|-------|
> | <operation description> | <tool name> | <any relevant notes or —> |
>
> ### Partially Covered (<count>)
> | Operation | Closest Tool | Gap |
> |-----------|-------------|-----|
> | <operation description> | <tool name> | <what's missing> |
>
> ### Not Covered (<count>)
> | Operation | Category | Evidence |
> |-----------|----------|----------|
> | <operation description> | <category> | <file path or context> |
>
> ### MCP-Addressable (<count>)
> | Operation | Suggested MCP Server | Rationale |
> |-----------|---------------------|-----------|
> | <operation description> | <server name> | <why this fits MCP> |
>
> ## Recommendations
> Prioritized list of missing capabilities, ordered by how many operations in this PR needed them:
>
> 1. **<capability>** — needed for: <list of operations that required this>
> 2. **<capability>** — needed for: <list of operations>
> ...
>
> ## Readiness Summary
> - **Covered**: X/Y operations (Z%)
> - **Partially covered**: X/Y operations (Z%)
> - **Not covered**: X/Y operations (Z%)
> - **MCP-addressable**: X/Y operations (Z%)
> ```
>
> **Rules:**
> - Every classified operation MUST appear in exactly one coverage table
> - Counts must add up: covered + partially covered + not covered + MCP-addressable = total operations
> - Recommendations must be ordered by frequency (most operations first)
> - Do not editorialize — factual assessments only
> - If an operation is both "not covered" AND "MCP-addressable", put it in MCP-addressable (the more actionable bucket)
````

- [ ] **Step 2: Verify all three agents are present**

Run: `grep "^## Agent [123]" /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected:
```
## Agent 1: PR Context Extractor
## Agent 2: Operation Classifier
## Agent 3: Gap Analyzer
```

- [ ] **Step 3: Commit**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "feat(gap-analysis): add Agent 3 gap analyzer prompt and report template"
```

---

### Task 7: Add Error Handling Section

**Files:**
- Modify: `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

- [ ] **Step 1: Append the error handling section**

Append to `/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`:

````markdown

## Error Handling

| Scenario | Behavior |
|----------|----------|
| No PR number provided | Print: "Usage: /gap-analysis <PR-number>" and stop |
| Invalid PR number | Print the `gh` error message and stop |
| `gh` not installed | Print: "gh CLI is required. Install from https://cli.github.com/" and stop |
| `gh` not authenticated | Print: "gh CLI not authenticated. Run `gh auth login` first." and stop |
| PR has 500+ changed files | Proceed with warning prepended to Agent 2 prompt |
| Agent subagent failure | Surface the error message and stop — do not retry |

No retry logic. No silent fallbacks. Errors surface immediately and halt execution.
````

- [ ] **Step 2: Commit**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "feat(gap-analysis): add error handling section"
```

---

### Task 8: Test the Skill End-to-End

**Files:**
- None modified — validation only

- [ ] **Step 1: Verify skill file structure is correct**

Run: `head -5 /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected: YAML frontmatter starting with `---`

- [ ] **Step 2: Verify skill is discoverable**

Run: `ls -la /home/bluehotdog/.claude/skills/gap-analysis/`
Expected: `SKILL.md` file present

- [ ] **Step 3: Count all major sections**

Run: `grep "^## " /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`
Expected sections (in order):
```
## Prerequisites
## Step 1: Validate Input
## Step 2: Orchestrate Three-Agent Pipeline
## Agent 1: PR Context Extractor
## Agent 2: Operation Classifier
## Frontman Tool Inventory (Static Snapshot — April 2026)
## Agent 3: Gap Analyzer
## Error Handling
```

- [ ] **Step 4: Run the skill against a real PR**

Invoke: `/gap-analysis <pick-a-recent-PR-number>`
Expected: The skill validates the PR, dispatches three agents sequentially, and produces a markdown report with all four coverage tables and a readiness summary. Verify:
- All operations appear in exactly one table
- Counts add up correctly
- Recommendations are ordered by frequency
- Report renders cleanly as markdown

- [ ] **Step 5: Run against an invalid PR number**

Invoke: `/gap-analysis 999999`
Expected: Error message about PR not found, execution stops cleanly.

- [ ] **Step 6: Final commit if any fixes were needed**

```bash
git add /home/bluehotdog/.claude/skills/gap-analysis/SKILL.md
git commit -m "fix(gap-analysis): address issues found during end-to-end testing"
```

Only commit this if Step 4 or 5 revealed issues that needed fixing.
