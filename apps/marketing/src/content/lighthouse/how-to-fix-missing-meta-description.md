---
title: "How to Fix Missing Meta Description"
description: "Lighthouse flags pages without a meta description tag. Learn how to write effective meta descriptions that improve click-through rates from search results."
pubDate: 2026-03-10T00:00:00Z
auditId: "meta-description"
category: "seo"
weight: 1
faq:
  - question: "What is a meta description?"
    answer: "A meta description is an HTML element (<meta name='description' content='...'>) that provides a brief summary of the page. Search engines often display it as the snippet text under the page title in search results."
  - question: "Does meta description affect SEO ranking?"
    answer: "Google has stated that meta descriptions are not a direct ranking factor. However, a compelling meta description improves click-through rate (CTR), which can indirectly affect rankings. A page with no description relies on Google to auto-generate a snippet, which may not be ideal."
  - question: "How long should a meta description be?"
    answer: "Keep meta descriptions between 120 and 160 characters. Google truncates longer descriptions in search results. Mobile results show fewer characters (~120) than desktop (~155-160)."
  - question: "Can Frontman fix missing meta descriptions automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Document does not have a meta description," it means the page is missing the [`<meta name="description">`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/meta) tag in `<head>`. This is an SEO audit — without a meta description, you leave the search result snippet entirely up to Google's auto-generation, which often produces suboptimal excerpts.

## Why Meta Descriptions Matter

- **Search result snippets** — Google displays the meta description as the preview text below the page title. A well-written description tells users what the page contains before they click
- **Click-through rate** — Pages with compelling, relevant descriptions get more clicks than those with auto-generated snippets. Google's [meta description documentation](https://developers.google.com/search/docs/appearance/snippet) explains how snippets are generated
- **Social sharing** — When someone shares your URL on social media or Slack, the meta description often appears as the preview text
- **User intent matching** — A clear description helps users decide if the page matches what they are looking for, reducing bounce rates

## The Old Way to Fix It

1. Run Lighthouse or check the page source for the meta description tag
2. Determine the page's primary topic and purpose
3. Write a 120-160 character description that includes the primary keyword and a clear value proposition
4. Add `<meta name="description" content="...">` to the `<head>` of the page
5. For dynamic pages (blogs, products), update the template to pull descriptions from the CMS or frontmatter
6. Verify with a tool like Google's Rich Results Test or an SEO checker
7. Repeat for every page missing a description

For sites with hundreds of pages, this becomes a content audit project — each page needs a unique, relevant description.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. Because Frontman sees the rendered page, it understands the content and can write a contextually appropriate description — not a placeholder. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Add a unique meta description to every page** — No two pages should share the same description. Each description should reflect the specific content of that page
- **Include primary keywords naturally** — Google bolds keywords that match the user's search query in the snippet, increasing visual prominence
- **Keep it between 120-160 characters** — Too short wastes space; too long gets truncated. Aim for the sweet spot
- **Write for humans, not algorithms** — The description's job is to convince a human to click. Write it as a concise pitch for the page content
- **Use your framework's head management** — Next.js `<Head>`, Astro frontmatter, Remix `meta` function — every framework has a pattern for managing meta tags
- **Add descriptions to CMS templates** — Make description a required field in your content model so editors cannot publish without one

## People Also Ask

### Does Google always use my meta description?

No. Google rewrites the snippet about 60-70% of the time, choosing text from the page that better matches the user's query. However, a good meta description increases the chance Google uses it verbatim, especially for branded or navigational searches.

### Should I use the same meta description for similar pages?

No. Duplicate meta descriptions across pages confuse search engines and provide no value. Each page should have a unique description. For programmatically generated pages (e.g., product pages), use templates that include page-specific data.

### What about Open Graph descriptions?

The `<meta property="og:description">` tag controls the description shown in social media previews (Facebook, LinkedIn, Twitter/X). It is separate from the search meta description. Ideally, set both — the meta description for search engines and the OG description for social sharing.

### Can an empty meta description hurt SEO?

An empty `<meta name="description" content="">` is treated the same as a missing description — Google will auto-generate a snippet. It does not hurt rankings directly, but you miss the opportunity to control your search appearance.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
