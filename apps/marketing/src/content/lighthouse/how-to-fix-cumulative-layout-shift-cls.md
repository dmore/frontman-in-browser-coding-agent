---
title: "How to Fix Cumulative Layout Shift (CLS)"
description: "Cumulative Layout Shift measures unexpected visual movement on the page. Learn how to find layout shift sources and eliminate them for a score under 0.1."
pubDate: 2026-03-10T00:00:00Z
auditId: "cumulative-layout-shift"
category: "performance"
weight: 25
faq:
  - question: "What is a good CLS score?"
    answer: "A CLS score of 0.1 or less is good. Between 0.1 and 0.25 needs improvement. Above 0.25 is poor. Lighthouse uses log-normal scoring with a p10 of 0.1 and a median of 0.25."
  - question: "What causes layout shifts?"
    answer: "Images without dimensions, dynamically injected content, web fonts causing text reflow (FOIT/FOUT), ads or embeds loading late, and CSS animations that trigger layout changes."
  - question: "Does CLS affect SEO ranking?"
    answer: "Yes. CLS is one of the three Core Web Vitals that Google uses as a ranking signal in search results."
  - question: "Can Frontman fix CLS issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags Cumulative Layout Shift, it means elements on the page moved unexpectedly after rendering. CLS carries a **weight of 25** in the Performance score — tied with [LCP](/lighthouse/how-to-fix-largest-contentful-paint-lcp/) as the second-heaviest metric. Every visible element that shifts position without user interaction contributes to the score.

Lighthouse calculates CLS by multiplying the fraction of the viewport affected by the shift (impact fraction) by the distance the element moved (distance fraction). These individual shift scores accumulate across the page load.

## Common Causes of Layout Shifts

- **Images and videos without explicit dimensions** — The browser reserves zero space until the asset loads, then pushes surrounding content down
- **Web fonts causing text reflow** — The fallback font renders first, then the web font loads and changes line heights, character widths, or both
- **Dynamically injected content** — Banners, cookie consent bars, or ads inserted above existing content push everything down
- **Late-loading embeds** — Third-party iframes (YouTube, Twitter, ads) that resize after loading
- **CSS animations triggering layout** — Animating `width`, `height`, `top`, `left`, or `margin` instead of `transform` and `opacity`

## The Old Way to Fix It

1. Run Lighthouse in DevTools and find the CLS score
2. Open the Performance panel, record a page load, and look for "Layout Shift" entries
3. Hover over each shift to see which element moved — the overlay highlights the before and after positions
4. Switch to your editor, find the component responsible
5. Add explicit `width` and `height` attributes to images
6. Add `font-display: swap` or `optional` to your `@font-face` rules
7. Add `aspect-ratio` CSS for responsive containers
8. Re-run Lighthouse and check the CLS score
9. Repeat for each shifting element

Layout shifts are particularly hard to debug because they are timing-dependent. A shift that appears on a throttled mobile connection may not appear on a fast desktop connection.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not hunt for shifting elements in the Performance panel or manually trace them back to components. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes for CLS

- **Set explicit dimensions on images and videos** — Always include `width` and `height` attributes, or use CSS `aspect-ratio`
- **Reserve space for dynamic content** — Use `min-height` on containers that load content asynchronously
- **Use `font-display: swap` or `optional`** — Prevents invisible text and reduces reflow when web fonts load. See the [font loading strategy](https://frontman.sh/glossary/font-loading-strategy/) glossary entry
- **Avoid inserting content above existing content** — Place banners, ads, and notices in reserved slots
- **Use `transform` for animations** — Animate `transform` and `opacity` instead of layout-triggering properties like `width`, `height`, or `margin`
- **Use `content-visibility: auto`** — For off-screen sections, this tells the browser to skip layout until the element is near the viewport

## People Also Ask

### What is considered a layout shift?

A layout shift occurs when a visible element changes its start position between two animation frames without being triggered by user interaction. Clicks, taps, and keypresses within 500 ms are excluded — those are expected shifts.

### How is CLS calculated?

CLS is the sum of individual layout shift scores. Each score equals the impact fraction (percentage of viewport affected) multiplied by the distance fraction (how far the element moved relative to the viewport). Lighthouse uses a windowing approach that groups shifts within 5-second windows and reports the worst window.

### Does lazy loading cause CLS?

Lazy loading itself does not cause CLS if the container has reserved dimensions. The issue arises when a lazy-loaded image's container has no explicit size — the container starts at zero height and expands when the image loads, shifting surrounding content.

### Can CSS `contain` property help with CLS?

Yes. `contain: layout` tells the browser that an element's contents do not affect layout outside it. This prevents shifts inside the element from propagating to siblings and ancestors.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
