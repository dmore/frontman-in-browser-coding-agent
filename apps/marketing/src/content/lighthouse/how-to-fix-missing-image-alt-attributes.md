---
title: "How to Fix Missing Image Alt Attributes"
description: "Lighthouse flags images without alt attributes because screen readers cannot describe them to users. Learn how to write effective alt text for every image on your page."
pubDate: 2026-03-10T00:00:00Z
auditId: "image-alt"
category: "accessibility"
weight: 10
faq:
  - question: "What is image alt text?"
    answer: "Alt text is the value of the alt attribute on an <img> element. Screen readers announce it when a user encounters the image. It also displays when the image fails to load. Search engines use it to understand image content."
  - question: "Should decorative images have alt text?"
    answer: "Decorative images that add no information should have an empty alt attribute: alt=''. This tells screen readers to skip the image entirely. Do not omit the alt attribute — that makes the screen reader announce the filename instead."
  - question: "How long should alt text be?"
    answer: "Keep alt text under 125 characters. It should describe the image concisely and convey the same information a sighted user gets from looking at the image. Avoid starting with 'Image of' or 'Picture of' — screen readers already announce it as an image."
  - question: "Can Frontman fix missing alt attributes automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Image elements do not have `[alt]` attributes," it means one or more `<img>` tags on the page are missing the `alt` attribute entirely. This is a **weight-10 accessibility audit** — the highest weight in the Accessibility category. Missing alt text means screen reader users encounter images with no description, often hearing just the filename or URL.

This audit also appears in the SEO category. Search engines use alt text to understand image content and context. Missing alt text means missed indexing opportunities.

## Why Alt Text Matters

- **Screen reader users** rely on alt text to understand what an image conveys. Without it, the screen reader reads the filename (`hero-banner-v3-final.jpg`) or nothing at all
- **Search engines** index alt text to understand image content. Google Images relies on it for image search results
- **Broken image fallback** — When an image fails to load, the browser shows the alt text in its place
- **Legal compliance** — WCAG 2.1 Level A (guideline 1.1.1) requires text alternatives for all non-text content

## The Old Way to Fix It

1. Run Lighthouse or an accessibility scanner and collect the list of images without alt attributes
2. For each image, open your editor and find the component that renders it
3. Determine what the image conveys — is it informational, decorative, or functional?
4. Write appropriate alt text: descriptive for informational images, empty (`alt=""`) for decorative ones
5. For images from a CMS, update the content model to require alt text on all image fields
6. Re-run the audit to verify

The challenge scales with the number of images. A page with 30 images and no alt attributes means finding 30 components, understanding each image's purpose, and writing 30 descriptions.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. Because Frontman lives in the browser, it can see each image in context and write appropriate alt text — not just generic descriptions. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Add descriptive alt text** to every informational image. Describe what the image shows in the context of the page, not the file name
- **Use empty alt (`alt=""`)** for decorative images — dividers, background patterns, spacers. This tells screen readers to skip the image
- **Never omit the alt attribute** — `<img src="photo.jpg">` without alt is worse than `alt=""`. The screen reader falls back to reading the file path
- **Avoid redundant alt text** — If a caption already describes the image, use `alt=""` on the image to avoid repeating the same information
- **Make functional images descriptive of their action** — An image used as a link or button should describe the action: `alt="Go to homepage"`, not `alt="logo.png"`
- **Add alt text fields in your CMS** — Make alt text a required field on image uploads to prevent the problem at the content level

## People Also Ask

### What is the difference between alt text and title attributes?

The `alt` attribute provides a text alternative when the image cannot be seen (screen readers, broken images). The `title` attribute shows a tooltip on hover and is not reliably announced by screen readers. Use `alt` for accessibility — `title` is optional and supplementary.

### Do background images need alt text?

CSS `background-image` elements do not have an `alt` attribute. If a background image is informational (contains text, data, or important content), use an `<img>` tag instead, or add `role="img"` and `aria-label` to the element.

### How does alt text affect [SEO](/lighthouse/how-to-fix-missing-meta-description/)?

Alt text helps search engines understand image content. Google uses it for Google Images ranking and as a secondary signal for page relevance. Descriptive, keyword-relevant alt text improves the chance of appearing in image search results.

### Should I use AI-generated alt text?

AI-generated alt text (from services like Azure Computer Vision or Google Cloud Vision) provides a starting point, but it often misses context. A photo of your team in front of the office might get "group of people standing outside a building" — accurate but not useful. Review and adjust AI suggestions for context and accuracy.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
