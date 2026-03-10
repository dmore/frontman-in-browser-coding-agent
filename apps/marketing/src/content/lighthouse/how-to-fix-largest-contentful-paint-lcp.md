---
title: "How to Fix Largest Contentful Paint (LCP)"
description: "Largest Contentful Paint measures how long the biggest visible element takes to render. Here is how to diagnose slow LCP and bring it under the 2.5-second threshold."
pubDate: 2026-03-10T00:00:00Z
auditId: "largest-contentful-paint"
category: "performance"
weight: 25
faq:
  - question: "What is a good LCP score?"
    answer: "Google considers LCP good when it is 2.5 seconds or less. Between 2.5 and 4 seconds needs improvement. Above 4 seconds is poor. Lighthouse scores LCP on a log-normal distribution with a p10 of 1,800 ms and a median of 3,000 ms."
  - question: "What elements count as the LCP element?"
    answer: "The LCP element is the largest image, video poster, background image painted via url(), or block-level text element visible in the viewport when the page loads. It changes as the page renders — the final LCP element is the largest one visible when the page becomes interactive."
  - question: "Does lazy loading affect LCP?"
    answer: "Yes. If you lazy-load the hero image that is also the LCP element, the browser delays its download until the element enters the viewport. This inflates LCP significantly. Never lazy-load above-the-fold images."
  - question: "Can Frontman fix LCP issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags Largest Contentful Paint, it means the biggest visible element in the viewport took too long to render. LCP carries a **weight of 25** in the [Lighthouse Performance score](https://web.dev/articles/lcp) — the second-heaviest metric after [Total Blocking Time](/lighthouse/how-to-fix-total-blocking-time-tbt/). A slow LCP drags the entire Performance category.

The LCP element is usually a hero image, a large heading, or a video poster frame. Lighthouse tells you which element it picked and how long it took to paint.

## Why LCP Is Slow

LCP breaks down into four phases:

- **Time to First Byte (TTFB)** — How long the server takes to respond to the initial HTML request
- **Resource load delay** — Time between TTFB and when the browser starts loading the LCP resource
- **Resource load duration** — How long the LCP resource (image, font, etc.) takes to download
- **Element render delay** — Time between when the resource finishes loading and when the element actually paints

Most LCP failures fall into one of these:

1. **Oversized hero images** — A 3 MB JPEG that should be 200 KB as a WebP
2. **Late-discovered images** — The LCP image is loaded via CSS `background-image` or JavaScript, so the browser's preload scanner cannot find it
3. **Render-blocking resources** — CSS and synchronous scripts block rendering before the LCP element can paint
4. **Lazy-loaded hero images** — `loading="lazy"` on an above-the-fold image delays its download

## The Old Way to Fix It

1. Open Chrome DevTools and run a Lighthouse audit
2. Find the "Largest Contentful Paint element" diagnostic
3. Identify which image or element Lighthouse flagged
4. Switch to your editor, find the component that renders that element
5. Compress the image manually or convert it to WebP/AVIF using a tool like Squoosh or ImageMagick
6. Add `fetchpriority="high"` to the `<img>` tag
7. Add a `<link rel="preload">` tag in the `<head>` for the image
8. Remove `loading="lazy"` if present on the hero image
9. Re-run Lighthouse in DevTools to check if LCP improved
10. Repeat until the score moves

This loop involves constant context switching between DevTools, the editor, and image compression tools. Each cycle takes minutes, and you often need three or four iterations.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not copy scores, translate audit names into code changes, or switch tabs. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes for LCP

- **Preload the LCP image** — Add [`<link rel="preload">`](https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/rel/preload) with `as="image"` in `<head>` so the browser discovers it immediately
- **Use [`fetchpriority="high"`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img#fetchpriority)** — Tells the browser to prioritize this image over other resources
- **Serve modern formats** — WebP is 25–35% smaller than JPEG. AVIF is 50% smaller. Use `<picture>` with fallbacks
- **Remove lazy loading on hero images** — `loading="lazy"` on above-the-fold images delays LCP
- **Inline critical CSS** — [Render-blocking CSS](/lighthouse/how-to-fix-render-blocking-resources/) delays the first paint, which delays LCP
- **Reduce server response time** — A slow TTFB delays everything downstream. Target under 200 ms

## People Also Ask

### What counts as the LCP element?

The LCP element is the largest image, video poster, or block-level text element visible in the viewport at load time. The browser updates the LCP candidate as the page renders — the final value is the last largest element before user interaction.

### How does LCP differ from FCP?

[First Contentful Paint](/lighthouse/how-to-fix-first-contentful-paint-fcp/) measures when any content appears on screen. LCP measures when the *largest* content element finishes rendering. FCP might fire when a small loading spinner appears; LCP fires when the hero image finishes painting.

### Does font loading affect LCP?

Yes. If the LCP element is a text block rendered with a web font, the browser waits for the font to download before painting the text. Use `font-display: swap` or `font-display: optional` to avoid invisible text during font loading.

### Is LCP a Core Web Vital?

LCP is one of the three [Core Web Vitals](https://frontman.sh/glossary/core-web-vitals/) used by Google as a ranking signal. The other two are [Cumulative Layout Shift](/lighthouse/how-to-fix-cumulative-layout-shift-cls/) and [Interaction to Next Paint](https://frontman.sh/glossary/interaction-to-next-paint/).

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
