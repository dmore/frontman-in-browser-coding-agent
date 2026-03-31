---
title: "How to Fix Unused JavaScript"
description: "Lighthouse flags JavaScript that is downloaded but never executed during page load. Learn how to identify dead code and reduce your JavaScript bundle size."
pubDate: 2026-03-10T00:00:00Z
auditId: "unused-javascript"
category: "performance"
weight: 0
faq:
  - question: "How does Lighthouse detect unused JavaScript?"
    answer: "Lighthouse uses Chrome's code coverage tool to track which JavaScript bytes execute during page load. Any code that downloads but does not execute is flagged as unused. This includes entire modules, unused exports, and polyfills for features the browser already supports."
  - question: "Is some unused JavaScript unavoidable?"
    answer: "Yes. Code that handles user interactions (click handlers, form validation) downloads with the page but only executes when the user interacts. Lighthouse flags it as unused because it did not run during the load trace. The goal is to reduce unnecessary code, not eliminate all flagged bytes."
  - question: "Does tree shaking remove all unused JavaScript?"
    answer: "Tree shaking removes unused exports at build time, but it only works with ES modules (import/export). CommonJS modules (require/module.exports) cannot be tree-shaken. Side effects in modules can also prevent removal."
  - question: "Can Frontman fix unused JavaScript automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags unused JavaScript, it means your page is downloading JavaScript that never executes during load. This wastes bandwidth and increases [Total Blocking Time](/lighthouse/how-to-fix-total-blocking-time-tbt/) because the browser must parse all downloaded JavaScript, even the parts it never runs.

Lighthouse shows each script file with the total bytes transferred and the bytes that went unused. A 400 KB bundle where 280 KB is unused means the user downloads 70% more JavaScript than the page needs.

## Why There Is Unused JavaScript

- **No code splitting** — The entire application ships in one bundle, including code for pages the user has not visited
- **Unused library features** — Importing all of lodash when you use three functions. Importing all of Material UI when you use two components
- **Legacy polyfills** — Babel polyfills for `Promise`, `Array.from`, or `Object.assign` shipped to browsers that already support them natively
- **Dead code** — Features that were removed from the UI but whose code still ships in the bundle
- **Third-party bloat** — Analytics SDKs, chat widgets, or A/B testing libraries loading their full code upfront

## The Old Way to Fix It

1. Run Lighthouse and find the unused JavaScript audit
2. Open Chrome DevTools Coverage tab, reload the page, and sort by unused bytes
3. Identify which scripts have the most unused code
4. Analyze the bundle with a source map explorer (`source-map-explorer`, `webpack-bundle-analyzer`, or Vite's `rollup-plugin-visualizer`)
5. For each problem:
   - Switch from full-library imports to specific imports (`import debounce from 'lodash/debounce'`)
   - Add dynamic `import()` for route-specific code
   - Configure Babel to target modern browsers and remove unnecessary polyfills
   - Remove dead code manually
6. Rebuild and re-run Lighthouse
7. Repeat

This process requires switching between multiple tools and significant knowledge of the build toolchain.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not dig through bundle analyzers or the Coverage tab. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Code split by route** — Use dynamic [`import()`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/import) so each page only loads its own code. Next.js, Remix, and Vite handle route-level splitting automatically, but shared component bundles may still need manual splitting
- **Use specific imports** — `import { debounce } from 'lodash-es'` instead of `import _ from 'lodash'`. Better yet, use `lodash-es` which is tree-shakeable
- **Remove unused polyfills** — Update your `browserslist` config to target only browsers you actually support. Use `@babel/preset-env` with `useBuiltIns: 'usage'` to include only needed polyfills
- **Enable tree shaking** — Use [ES modules](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Modules) (`import`/`export`) so your bundler can remove unused exports. Mark packages as side-effect-free in `package.json` with `"sideEffects": false`
- **Lazy-load third-party scripts** — Load analytics, chat widgets, and ad scripts after the page is interactive
- **Audit your dependencies** — Use [bundlephobia.com](https://bundlephobia.com) to check package sizes before adding them. Use `source-map-explorer` to visualize what is in your bundle

## People Also Ask

### How much unused JavaScript is too much?

Lighthouse flags any script where a significant portion goes unused. As a rule of thumb, if more than 20% of a script's bytes are unused, investigate. Scripts over 50 KB with more than 50% unused code should be split or replaced.

### Does server-side rendering help with unused JavaScript?

SSR helps [FCP](/lighthouse/how-to-fix-first-contentful-paint-fcp/) and [LCP](/lighthouse/how-to-fix-largest-contentful-paint-lcp/) because the browser receives pre-rendered HTML. But the same JavaScript still ships for hydration. To reduce unused JS in SSR apps, use React Server Components, partial hydration, or island architecture such as Astro.

### What is the difference between unused JavaScript and unminified JavaScript?

[Unminified JavaScript](/lighthouse/how-to-fix-unminified-javascript/) means the file contains whitespace, comments, and long variable names that add bytes. Unused JavaScript means the file contains code that downloads but never executes. They are separate issues — code can be minified but still unused.

### Does dynamic import() increase the number of network requests?

Yes. Each dynamic import creates a separate chunk that loads on demand. This trades fewer initial bytes for more requests later. With HTTP/2 multiplexing, this tradeoff almost always favors splitting.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
