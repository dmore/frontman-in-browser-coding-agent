---
title: "How to Fix Links Without a Discernible Name"
description: "Lighthouse flags links that screen readers cannot identify. Learn how to add descriptive text to every link so users understand where it leads."
pubDate: 2026-03-10T00:00:00Z
auditId: "link-name"
category: "accessibility"
weight: 7
faq:
  - question: "What is a discernible link name?"
    answer: "A discernible name is text that tells users where a link goes or what it does. It can come from visible text content, an aria-label, an aria-labelledby reference, or the alt text of a contained image."
  - question: "Why is 'click here' a bad link name?"
    answer: "Screen reader users often navigate by links — they tab through or list all links on a page. When every link says 'click here' or 'read more,' users cannot distinguish between them. Descriptive text like 'View pricing details' tells users exactly where the link goes."
  - question: "Do image links need special treatment?"
    answer: "Yes. A link that contains only an image needs alt text on the image to provide the link's accessible name. Without it, screen readers announce the image URL or nothing at all."
  - question: "Can Frontman fix link accessibility issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Links do not have a discernible name," it means one or more [`<a>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a) elements have no text that screen readers can announce. This is a **weight-7 accessibility audit**. Screen reader users navigating by links hear "link" with no description of where it goes.

Lighthouse checks for text content, `aria-label`, `aria-labelledby`, and `alt` text on contained images. If none exist, the link fails.

## Why Links Are Missing Names

- **Image-only links** — A logo image wrapped in `<a>` without alt text on the image
- **Icon links** — Social media icons, arrow icons, or navigation icons with no text
- **Empty links** — `<a href="/page"></a>` with no content, often used for clickable overlays
- **Generic text** — While "click here" and "read more" technically pass the audit, they fail the spirit of accessibility
- **Links styled with CSS** — Links whose visible text comes from CSS `::before`/`::after` pseudo-elements

## The Old Way to Fix It

1. Run Lighthouse or a link accessibility audit
2. For each flagged link, find it in the source code
3. Determine where the link goes and what its purpose is
4. Add descriptive text, aria-label, or alt text to the contained image
5. For "read more" links, add `aria-label` with context: `aria-label="Read more about pricing"`
6. Re-run the audit

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. Because Frontman sees each link in the rendered page, it understands the link's destination and purpose — and writes accurate, descriptive labels. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Use descriptive visible text** — `<a href="/pricing">View pricing details</a>` is better than `<a href="/pricing">Click here</a>`
- **Add alt text to image links** — `<a href="/"><img src="logo.svg" alt="Homepage"></a>`
- **Use [`aria-label`](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Attributes/aria-label) for icon links** — `<a href="https://twitter.com/..." aria-label="Follow us on Twitter"><TwitterIcon /></a>`
- **Add context to generic links** — For "Read more" links, use `aria-label` to add context: `<a href="/blog/post" aria-label="Read more about performance optimization">Read more</a>`
- **Use visually hidden text** — `<a href="/search"><span class="sr-only">Search</span><SearchIcon /></a>` provides an accessible name without changing the visual design
- **Never use empty href** — `<a href="">` or `<a href="#">` with no text is meaningless to all users

## People Also Ask

### What makes a link name "discernible"?

A discernible name is text that distinguishes one link from another and tells the user where it leads. "View pricing" is discernible. "Click here" is not — five "click here" links on a page are indistinguishable when navigated by keyboard or screen reader.

### How is the link-name audit different from the [button-name audit](/lighthouse/how-to-fix-buttons-missing-accessible-name/)?

Links (`<a>`) navigate to URLs. Buttons (`<button>`) perform actions. They are tested by separate audits but the fix is the same: add text, `aria-label`, or `aria-labelledby` so the element has an accessible name.

### Should I use title attributes on links?

The `title` attribute shows a tooltip on hover but is not reliably announced by screen readers and is invisible to touch users. Do not rely on `title` as the only accessible name. Use visible text or `aria-label` instead.

### How do I handle links that open in a new tab?

Add visual and accessible indication: `<a href="..." target="_blank" rel="noopener">External resource (opens in new tab)</a>` or use an icon with `aria-label`. Users need to know the link will open a new window before they activate it.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
