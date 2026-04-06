# Gap Analysis Skill Design

## Purpose

A Claude Code skill (`/gap-analysis <PR-number>`) that analyzes a GitHub PR from the current repo and produces a gap analysis report identifying what tools, capabilities, and integrations Frontman is missing to have built that PR's feature itself.

**Audience**: Frontman product team — output is framed as actionable roadmap items.

## Invocation

```
/gap-analysis 123
```

- Accepts a PR number; assumes the current repo context
- Validates via `gh pr view <number>` before proceeding
- Reports error and stops if the PR doesn't exist or `gh` is not authenticated

## Architecture: Multi-Agent Pipeline

Three subagents run sequentially (each depends on the previous):

```
PR number
    │
    ▼
┌──────────────────────┐
│  Agent 1: PR Context │
│  Extractor           │
│                      │
│  gh pr view/diff     │
│  gh issue view       │
│  repo structure scan │
└──────────┬───────────┘
           │ PR context bundle
           ▼
┌──────────────────────┐
│  Agent 2: Operation  │
│  Classifier          │
│                      │
│  Categorize every    │
│  discrete operation  │
│  from the diff       │
└──────────┬───────────┘
           │ Classified operations
           ▼
┌──────────────────────┐
│  Agent 3: Gap        │
│  Analyzer            │
│                      │
│  Match operations    │
│  against tool        │
│  inventory, produce  │
│  report              │
└──────────────────────┘
           │
           ▼
     Markdown report
     (stdout)
```

---

## Agent 1: PR Context Extractor

**Input**: PR number + repo context

**Actions**:
- `gh pr view <number> --json title,body,labels,number` — PR metadata
- `gh pr diff <number>` — full diff
- `gh pr view <number> --json commits` — commit messages
- `gh pr view <number> --json comments` — review comments for additional context
- If the PR description references issues, fetch via `gh issue view`
- Examine repo structure around changed files (neighboring files, configs, CI workflows)

**Output**: Structured summary containing:
- PR purpose/intent (from description + issues)
- List of all files changed with change type (added/modified/deleted)
- Commit messages
- External systems referenced (CI configs, package.json changes, migration files, etc.)
- Raw diff for the classifier

---

## Agent 2: Operation Classifier

**Input**: PR Context Extractor output

**Job**: Categorize every discrete operation the PR required.

**Taxonomy**:

| Category | Examples |
|----------|----------|
| File I/O | read, write, edit, create, delete, rename, move |
| Code Understanding | search, navigate, inspect structure, trace dependencies |
| Browser/UI | visual inspection, DOM manipulation, interaction, responsive testing |
| Testing | unit test writing, test execution, test result interpretation |
| Build/Compile | compilation, bundling, type checking, lint |
| Source Control | branching, committing, rebasing, conflict resolution |
| Package Management | dependency add/remove/update, lockfile changes |
| Database | migrations, schema changes, seed data |
| CI/CD | workflow edits, pipeline triggers, artifact management |
| Infrastructure | deployment config, environment variables, secrets |
| External APIs | third-party service calls, webhook setup |
| Design/Assets | image creation, SVG editing, font changes |
| Documentation | README, changelog, inline docs, API docs |

**Output per operation**:
- Category (from taxonomy)
- Description of what was done
- Evidence (file path + relevant diff snippet)
- Confidence (high/medium/low)

**Large PR handling**: For PRs with 500+ changed files, process in batches grouped by directory/concern. Warn in output that analysis may be less precise.

---

## Agent 3: Gap Analyzer

**Input**: Classified operations + static tool inventory (embedded in skill)

**Coverage categories**:
- **Covered**: maps directly to an existing Frontman tool
- **Partially covered**: tool exists but lacks a required capability
- **Not covered**: no tool handles this operation
- **MCP-addressable**: not native, but solvable with an MCP server

---

## Static Tool Inventory (Embedded Snapshot)

### File Operations
- `read_file` — read with offset/limit pagination
- `write_file` — write content or image_ref, creates parent dirs
- `edit_file` — find-and-replace with fuzzy matching (exact, trimmed, whitespace-normalized, indentation-flexible)
- `file_exists` — check file/directory existence
- `search_files` — find files by name pattern
- `list_files` — list directory contents (.gitignore aware)
- `list_tree` — recursive tree view with workspace detection
- `grep` — search file contents, regex/literal/glob, ripgrep-based

### Browser Interaction
- `take_screenshot` — capture viewport or element, full-page support
- `get_dom` — inspect DOM subtrees, simplified/full modes, max depth/node limits
- `get_interactive_elements` — list buttons, inputs, links with ARIA info
- `interact_with_element` — click, hover, focus via selector/role/text
- `execute_js` — run arbitrary JS in preview iframe, console capture
- `search_text` — find visible text on page (like Ctrl+F)
- `set_device_mode` — responsive device emulation presets

### Human-in-the-Loop
- `question` — ask user questions, multiple choice + freetext, blocking

### Server/Utility
- `web_fetch` — fetch web pages as markdown, pagination, SSRF protection
- `todo_write` — atomic todo list management
- `lighthouse` — Google Lighthouse audits (performance, accessibility, SEO, best practices)
- `load_agent_instructions` — discover Agents.md/CLAUDE.md files

### Framework-Specific
- **Next.js**: `get_routes`, `get_logs`, `edit_file` override (webpack/turbopack error check)
- **Astro**: `get_client_pages`, `get_logs`, `edit_file` override
- **Vite**: `get_logs`, `edit_file` override

### Extensibility
- MCP server integration for external tool providers

### Known Limitations (No Current Tool)
- Terminal/shell execution
- Git operations (commit, branch, push, rebase)
- Package management (npm install, yarn add)
- Database operations (migrations, queries, seeds)
- CI/CD interaction (trigger builds, read pipeline logs)
- Test execution (run suites, read results)
- Environment/secret management
- Code generation/scaffolding
- Image/asset creation or manipulation
- API client tools (REST/GraphQL beyond web_fetch)
- Deployment operations

---

## Report Format

```markdown
# Gap Analysis: PR #<number> — <title>

## PR Summary
<2-3 sentence description of what this PR accomplished and why>

## Operations Breakdown
<total> operations identified across <N> categories

### Covered by Frontman (<count>)
| Operation | Tool | Notes |
|-----------|------|-------|
| Edit source file | edit_file | Fuzzy matching handles LLM formatting |
| Inspect DOM structure | get_dom | — |

### Partially Covered (<count>)
| Operation | Closest Tool | Gap |
|-----------|-------------|-----|
| Rename file | edit_file + write_file | No native rename — requires read+write+delete workaround |

### Not Covered (<count>)
| Operation | Category | Evidence |
|-----------|----------|----------|
| Run test suite | Testing | Modified test files, CI required passing tests |
| Database migration | Database | Added migration file `priv/repo/migrations/...` |

### MCP-Addressable (<count>)
| Operation | Suggested MCP Server | Rationale |
|-----------|---------------------|-----------|
| Git commit & push | git-mcp | Source control ops are well-defined, stateless per call |
| npm install | package-manager-mcp | Dependency resolution is an external process |

## Recommendations
Prioritized list of missing capabilities, ordered by frequency:

1. **Terminal/shell execution** — needed for: test runs, build commands, migrations
2. **Git operations** — needed for: committing changes, branch management
3. ...

## Readiness Summary
- **Covered**: X/Y operations (Z%)
- **Partially covered**: ...
- **Not covered**: ...
- **MCP-addressable**: ...
```

Output is written to stdout (displayed in conversation), not saved to a file.

---

## Error Handling

| Scenario | Behavior |
|----------|----------|
| Invalid PR number | Report `gh` error, stop |
| Massive PR (500+ files) | Batch processing in Agent 2, warn about precision |
| Docs/config-only PR | Valid — fewer categories in output |
| Closed/merged PR | Works fine — `gh pr diff` supports any state |
| Cross-repo PR | Out of scope — assumes current repo |
| `gh` auth failure | Surface error, stop |

No retry logic. No silent fallbacks. Errors surface and halt.

---

## Skill Location

`/home/bluehotdog/.claude/skills/gap-analysis/SKILL.md`

## Dependencies

- `gh` CLI authenticated with repo access
- Agent tool for subagent dispatch
