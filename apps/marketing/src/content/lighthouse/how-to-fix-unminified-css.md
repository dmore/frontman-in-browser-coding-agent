---
title: "How to Fix Unminified CSS"
description: "Lighthouse flags CSS files that contain unnecessary whitespace and comments. Learn how to minify your stylesheets and reduce transfer size."
pubDate: 2026-03-10T00:00:00Z
auditId: "unminified-css"
category: "performance"
weight: 0
faq:
  - question: "What is CSS minification?"
    answer: "CSS minification removes whitespace, comments, and unnecessary characters from stylesheets without changing their behavior. It also optimizes shorthand properties and removes duplicate rules."
  - question: "How much does CSS minification save?"
    answer: "Typical savings are 15-30% of file size. A well-structured 100 KB stylesheet usually becomes 70-85 KB after minification. Combined with Gzip or Brotli compression, transfer size can drop by 80-90%."
  - question: "Does CSS minification break styles?"
    answer: "No. Minification tools like cssnano, Lightning CSS, and clean-css preserve the behavior of every rule. They remove whitespace and comments, merge duplicate selectors, and shorten color values — all without changing rendered output."
  - question: "Can Frontman fix unminified CSS automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Minify CSS," it means your stylesheets contain whitespace, comments, and verbose syntax that add unnecessary bytes. CSS is [render-blocking](/lighthouse/how-to-fix-render-blocking-resources/) by default — every extra byte delays [First Contentful Paint](/lighthouse/how-to-fix-first-contentful-paint-fcp/).

Lighthouse shows the potential byte savings for each unminified CSS file.

## Why CSS Is Unminified

- **Development build in production** — CSS preprocessors (Sass, Less, PostCSS) output formatted CSS by default in development mode
- **Missing minification plugin** — PostCSS without cssnano, or Webpack without CSS optimization
- **External stylesheets from CDNs** — Using the non-minified version of a CSS framework (e.g., `bootstrap.css` instead of `bootstrap.min.css`)
- **Inline styles bypassing the build** — CSS written in `<style>` tags that does not go through the build pipeline

## The Old Way to Fix It

1. Run Lighthouse and identify flagged stylesheets
2. Check your CSS build pipeline for minification settings
3. Add cssnano, Lightning CSS, or clean-css to your PostCSS config
4. For external CSS: switch to `.min.css` versions
5. Rebuild, redeploy, re-test

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not configure PostCSS plugins or swap CDN URLs manually. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Use cssnano** — The most popular CSS minifier. Add it to your PostCSS pipeline: `postcss.config.js` → `plugins: [require('cssnano')]`
- **Use Lightning CSS** — A faster alternative to cssnano written in Rust. Vite uses it by default in production builds
- **Enable production mode** — Most frameworks minify CSS automatically in production. Verify `NODE_ENV=production` is set during build
- **Use `.min.css` versions** — For external libraries, always use the minified distribution file
- **Inline and minify critical CSS** — Tools like `critters` extract and minify critical CSS in one step
- **Combine with [unused CSS removal](/lighthouse/how-to-fix-unused-css/)** — Remove unused rules first, then minify what remains

## People Also Ask

### What is the difference between CSS minification and CSS purging?

Minification removes whitespace and comments from the CSS you use. Purging ([unused CSS removal](/lighthouse/how-to-fix-unused-css/)) removes entire rules that are never applied. They solve different problems — purging reduces the number of rules, minification compresses each rule.

### Does Tailwind CSS minify automatically?

Tailwind v3+ uses PostCSS and purges unused utilities by default in production. You can add cssnano as a PostCSS plugin for additional minification. Tailwind v4 uses Lightning CSS and includes minification in its build step.

### Can CSS minification cause issues with source maps?

No. CSS minifiers generate source maps that map the minified output back to the original source. Enable source maps in your cssnano or Lightning CSS configuration for debugging.

### Should I also compress CSS with Gzip or Brotli?

Yes. Minify first to reduce source size, then serve with Brotli compression (preferred) or Gzip. Most web servers and CDNs handle compression automatically. The combined savings are significant: a 100 KB stylesheet → 75 KB minified → 15 KB Brotli-compressed.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
