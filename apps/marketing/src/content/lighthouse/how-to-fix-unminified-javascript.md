---
title: "How to Fix Unminified JavaScript"
description: "Lighthouse flags JavaScript files that contain unnecessary whitespace, comments, and long variable names. Learn how to minify your scripts and reduce transfer size."
pubDate: 2026-03-10T00:00:00Z
auditId: "unminified-javascript"
category: "performance"
weight: 0
faq:
  - question: "What is JavaScript minification?"
    answer: "Minification removes whitespace, comments, and shortens variable names in JavaScript files without changing behavior. A 100 KB unminified file typically becomes 40-60 KB minified, reducing download time and parse time."
  - question: "Does minification break source maps?"
    answer: "No. Modern bundlers generate source maps alongside minified output. Source maps let DevTools show original, readable code while the browser runs the minified version."
  - question: "Is minification the same as compression?"
    answer: "No. Minification removes unnecessary characters from the source. Compression (Gzip, Brotli) encodes the file for smaller transfer size. They complement each other — minify first, then compress. A minified + Brotli-compressed file is the smallest."
  - question: "Can Frontman fix unminified JavaScript automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Minify JavaScript," it means your [`<script>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script) files contain whitespace, comments, and full-length variable names that add unnecessary bytes. These extra bytes increase download time and parse time. Lighthouse shows the potential savings for each unminified file.

This audit directly affects [Total Blocking Time](/lighthouse/how-to-fix-total-blocking-time-tbt/) (larger files take longer to parse) and [First Contentful Paint](/lighthouse/how-to-fix-first-contentful-paint-fcp/) (if the script is render-blocking).

## Why JavaScript Is Unminified

- **Development build deployed to production** — Running `npm start` instead of `npm run build`, or deploying without the production flag
- **Missing minification in build config** — Custom Webpack, Rollup, or Vite configurations that do not enable Terser or esbuild minification
- **Third-party scripts** — Vendor scripts loaded from CDNs that serve unminified versions (e.g., using `react.development.js` instead of `react.production.min.js`)
- **Inline scripts** — JavaScript written directly in HTML that bypasses the build pipeline

## The Old Way to Fix It

1. Run Lighthouse and note which scripts are flagged
2. Check your bundler configuration for minification settings
3. For Webpack: verify `mode: 'production'` is set, or add `TerserPlugin` to optimization
4. For Vite: verify `build.minify` is not set to `false`
5. For third-party scripts: find the `.min.js` version of each library
6. For inline scripts: extract them into the build pipeline or manually minify
7. Rebuild and redeploy
8. Re-run Lighthouse to verify

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not audit build configs or hunt for development-mode scripts. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Set production mode** — `NODE_ENV=production` enables minification in most bundlers. Webpack's `mode: 'production'`, Vite, and Next.js all minify automatically in production
- **Use Terser or esbuild** — Terser provides advanced minification with dead code elimination. esbuild is faster with slightly less compression. Most frameworks configure one of these by default
- **Use minified CDN versions** — Replace `.js` with `.min.js` for third-party scripts. Use CDNs like cdnjs or unpkg that serve minified versions
- **Minify inline scripts** — Extract inline `<script>` blocks into the build pipeline so they get minified with everything else
- **Enable source maps** — Minification makes debugging impossible without source maps. Generate them for development and staging; optionally disable for production
- **Combine with [Gzip/Brotli compression](/lighthouse/how-to-fix-unused-javascript/)** — Minification reduces source size; compression reduces transfer size. Use both

## People Also Ask

### How much does minification save?

Typical savings are 30–60% of file size. A 200 KB unminified bundle typically becomes 80–120 KB after [minification](https://en.wikipedia.org/wiki/Minification_(programming)). Combined with Brotli compression, the transfer size can drop to 30–50 KB.

### Does minification affect runtime performance?

Minification does not change runtime behavior. Shorter variable names and removed whitespace parse slightly faster, but the difference is negligible. The main benefit is smaller file size for faster downloads.

### Should I minify CSS too?

Yes. [Unminified CSS](/lighthouse/how-to-fix-unminified-css/) is a separate Lighthouse audit. CSS minification removes whitespace, comments, and shorthand-expands properties. Tools like cssnano, Lightning CSS, or PostCSS handle CSS minification.

### What is the difference between minification and obfuscation?

Minification shortens code for size. Obfuscation rewrites code to be hard to understand (renaming functions, adding dead code, encoding strings). Minification is a standard production practice; obfuscation is a separate security concern and is not recommended for most web applications.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
