---
title: "How to Fix Missing Document Title"
description: "Lighthouse flags pages without a title element. Learn how to add descriptive, unique page titles that improve SEO and accessibility."
pubDate: 2026-03-10T00:00:00Z
auditId: "document-title"
category: "seo"
weight: 1
faq:
  - question: "What is the document title?"
    answer: "The document title is the text inside the <title> element in the HTML <head>. It appears in the browser tab, bookmarks, search engine results, and is the first thing screen readers announce when a user opens a page."
  - question: "How long should a page title be?"
    answer: "Keep titles between 30 and 60 characters. Google displays approximately 50-60 characters before truncating. Shorter titles are easier to scan; longer titles risk losing important information."
  - question: "Should every page have a unique title?"
    answer: "Yes. Duplicate titles across pages confuse search engines and users. Each page should have a title that uniquely describes its content. For templated pages, include dynamic data like the product name or article title."
  - question: "Can Frontman fix missing titles automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Document does not have a [`<title>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/title) element," the page is missing a `<title>` tag in `<head>` — or the tag is present but empty. This audit appears in both the **SEO** and **Accessibility** categories.

Without a title, the browser tab shows the URL. Search engines have no headline for the result. Screen readers cannot announce what page the user is on.

## Why the Title Matters

- **Search engine results** — The title is typically the clickable headline in Google, Bing, and other search results. Google's [title link documentation](https://developers.google.com/search/docs/appearance/title-link) explains how titles appear in results. It is the strongest on-page SEO signal for relevance
- **Browser tabs** — Users with multiple tabs open use the title to find the right tab. A missing title shows a truncated URL
- **Screen readers** — The `<title>` is the first element announced when a user opens or navigates to a page
- **Social sharing** — The title appears in link previews on Twitter/X, Facebook, Slack, and other platforms (via `<meta property="og:title">`, which falls back to `<title>`)
- **Bookmarks** — Browsers use the title as the default bookmark name

## The Old Way to Fix It

1. Run Lighthouse or check the page source for the `<title>` element
2. Write a unique, descriptive title that includes the primary keyword
3. Add `<title>Your Page Title</title>` to the `<head>`
4. For dynamic pages, update the template to generate titles from content data
5. Verify each page has a unique title with a site crawler like Screaming Frog
6. Repeat for every page missing a title

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. Because Frontman sees the rendered page, it reads the content and writes a contextually accurate title — not a placeholder. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Add a `<title>` to every page** — Place it inside `<head>`, before any other elements
- **Format consistently** — Use a pattern like `Page Name | Site Name` or `Page Name — Site Name`
- **Include the primary keyword** — Place the most important keyword near the beginning of the title
- **Keep it under 60 characters** — Google truncates longer titles. Front-load important information
- **Make each title unique** — No two pages should share the same title
- **Use your framework's title management** — Next.js `metadata` object, Astro `<title>` in layout, Remix `meta` function, SvelteKit `<svelte:head>`

## People Also Ask

### Does the title tag directly affect SEO ranking?

Yes. The `<title>` element is one of the strongest on-page ranking signals. Google uses it to understand the page's topic and displays it as the headline in search results. A relevant, keyword-rich title improves both ranking and click-through rate.

### What is the difference between `<title>` and `<h1>`?

The `<title>` appears in the browser tab and search results — it is metadata. The `<h1>` appears on the page — it is content. They can be identical or different. The `<title>` often includes the site name (`Article Title | Site Name`), while the `<h1>` is just the article title.

### Does this audit also check for empty titles?

Yes. `<title></title>` and `<title> </title>` (whitespace-only) are treated the same as a missing title. Lighthouse requires the title to contain meaningful text.

### Should I change the title on single-page applications?

Yes. SPAs should update `document.title` on each route change so the browser tab, screen readers, and search engines see the correct title for each view. Most SPA frameworks (React Router, Vue Router) support this via route metadata or head management libraries.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
