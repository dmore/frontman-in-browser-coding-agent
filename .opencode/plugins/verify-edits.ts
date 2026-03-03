import type { Plugin } from "@opencode-ai/plugin"
import { readFileSync, existsSync } from "fs"
import { join, extname } from "path"

// Map file extensions to their AGENTS.md path (relative to project root)
const GUIDELINE_MAP: Record<string, string> = {
  ".res": "AGENTS.md",
  ".ex": "apps/frontman_server/AGENTS.md",
  ".exs": "apps/frontman_server/AGENTS.md",
}

const EDIT_TOOLS = new Set(["edit", "write", "apply_patch"])

// Extract file paths from apply_patch patchText
function extractPatchPaths(patchText: string): string[] {
  const regex = /\*\*\* (?:Add|Delete|Update) File:\s*(.+)/gm
  const paths: string[] = []
  for (const match of patchText.matchAll(regex)) {
    paths.push(match[1].trim())
  }
  return paths
}

// Given a file path, return the guidelines content (or null to skip)
function getGuidelines(filePath: string, directory: string): string | null {
  const ext = extname(filePath)
  const guidelinePath = GUIDELINE_MAP[ext]
  if (!guidelinePath) return null

  const fullPath = join(directory, guidelinePath)
  if (!existsSync(fullPath)) return null
  return readFileSync(fullPath, "utf-8")
}

// Get all affected file paths from tool args
function getFilePaths(tool: string, args: any): string[] {
  if (tool === "edit" || tool === "write") {
    return args.filePath ? [args.filePath] : []
  }
  if (tool === "apply_patch") {
    return extractPatchPaths(args.patchText ?? "")
  }
  return []
}

export default (async ({ client, directory }) => {
  // Pre-validate that at least one guidelines file exists
  const hasAnyGuidelines = Object.values(GUIDELINE_MAP).some((p) =>
    existsSync(join(directory, p)),
  )
  if (!hasAnyGuidelines) return {}

  return {
    "tool.execute.after": async (input, output) => {
      if (!EDIT_TOOLS.has(input.tool)) return

      const filePaths = getFilePaths(input.tool, input.args)
      if (filePaths.length === 0) return

      // Group file paths by their guidelines content (dedup)
      const guidelineGroups = new Map<string, string[]>()
      for (const fp of filePaths) {
        const guidelines = getGuidelines(fp, directory)
        if (!guidelines) continue
        const existing = guidelineGroups.get(guidelines) ?? []
        existing.push(fp)
        guidelineGroups.set(guidelines, existing)
      }

      if (guidelineGroups.size === 0) return

      // Verify each group (typically 1, at most 2 for mixed .res + .ex patches)
      const violations: string[] = []

      for (const [guidelines, files] of guidelineGroups) {
        let session: any
        try {
          session = await client.session.create({
            body: { title: `verify: ${input.tool}` },
          })

          const result = await client.session.prompt({
            path: { id: session.data!.id },
            body: {
              agent: "verify",
              tools: {},
              parts: [
                {
                  type: "text" as const,
                  text: [
                    "## Project Guidelines\n",
                    guidelines,
                    "\n## Edit to Verify\n",
                    `Tool: ${input.tool}`,
                    `Files: ${files.join(", ")}`,
                    `Args:\n\`\`\`json\n${JSON.stringify(input.args, null, 2)}\n\`\`\``,
                    `\nResult:\n${output.output}`,
                    "\n---",
                    "Does this edit conform to the guidelines above?",
                    "If there is one small thing you could do to simplify this code — removing lines or reducing complexity, not adding — suggest it.",
                    "Reply PASS or FAIL: <violations>. Add SIMPLIFY: <suggestion> if applicable.",
                  ].join("\n"),
                },
              ],
            },
          })

          const textPart = result.data?.parts?.find(
            (p: any) => p.type === "text",
          ) as { type: "text"; text: string } | undefined
          const text = textPart?.text?.trim() ?? ""

          if (text !== "PASS" && text.length > 0) {
            violations.push(text)
          }
        } catch (err) {
          // Never block the main agent on verification failure
          console.error("[verify-edits] Verification error:", err)
        } finally {
          if (session?.data?.id) {
            await client.session
              .delete({ path: { id: session.data.id } })
              .catch(() => {})
          }
        }
      }

      if (violations.length > 0) {
        output.output +=
          "\n\n⚠️ AGENTS.md Verification:\n" + violations.join("\n")
      }
    },
  }
}) satisfies Plugin
