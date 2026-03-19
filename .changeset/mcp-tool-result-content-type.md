---
"@frontman-ai/frontman-protocol": minor
"@frontman-ai/frontman-core": patch
"@frontman-ai/client": patch
---

Replace raw string `type_` field in `toolResultContent` with a typed `toolResultContentType` variant (`Text | Image | Resource`) per MCP spec. Provides compile-time validation that content type values are valid — typos like `"txt"` are now caught at build time.
