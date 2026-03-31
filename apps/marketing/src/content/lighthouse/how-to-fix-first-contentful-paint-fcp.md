---
title: "How to Fix First Contentful Paint (FCP)"
description: "First Contentful Paint measures how quickly any content appears on screen. Learn how to eliminate delays and get FCP under 1.8 seconds."
pubDate: 2026-03-10T00:00:00Z
auditId: "first-contentful-paint"
category: "performance"
weight: 10
faq:
  - question: "What is a good FCP score?"
    answer: "Google considers FCP good at 1.8 seconds or less. Between 1.8 and 3 seconds needs improvement. Above 3 seconds is poor."
  - question: "What is the difference between FCP and LCP?"
    answer: "FCP fires when any content renders — even a loading spinner or navigation bar. LCP fires when the largest visible element finishes rendering. FCP is about speed of first paint; LCP is about speed of meaningful content."
  - question: "Does server response time affect FCP?"
    answer: "Yes. FCP cannot happen until the browser receives the HTML response. A slow TTFB directly delays FCP. Target a server response time under 200 ms."
  - question: "Can Frontman fix FCP issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags [First Contentful Paint](https://web.dev/articles/fcp), it means the browser took too long to render any text, image, SVG, or non-white canvas element. FCP carries a **weight of 10** in the Performance score. While it is lighter than [LCP](/lighthouse/how-to-fix-largest-contentful-paint-lcp/) and [TBT](/lighthouse/how-to-fix-total-blocking-time-tbt/), a slow FCP usually indicates problems that cascade into other metrics.

FCP is the earliest signal that the page is loading. Users perceive a blank screen as broken — even one second of nothing feels slow.

## Why FCP Is Slow

- **Slow server response time (TTFB)** — The browser cannot paint anything until the HTML arrives. A TTFB over 600 ms delays everything
- **[Render-blocking resources](/lighthouse/how-to-fix-render-blocking-resources/)** — Synchronous CSS and JavaScript in `<head>` prevent the browser from painting until they finish downloading and executing
- **Large CSS files** — The browser must parse all CSS before rendering. A 200 KB stylesheet blocks the first paint
- **Web font blocking** — Fonts loaded with `font-display: block` delay text rendering until the font downloads
- **Redirect chains** — Each HTTP redirect adds a full round trip before the browser even starts loading the final page

## The Old Way to Fix It

1. Run Lighthouse and note the FCP time
2. Check the Network panel waterfall for render-blocking resources
3. Identify which CSS and JS files block rendering
4. Manually extract critical CSS (above-the-fold styles) and inline it
5. Defer non-critical CSS with `media="print"` or dynamic loading
6. Add `defer` or `async` to script tags
7. Add `font-display: swap` to web font declarations
8. Eliminate redirect chains in your server config
9. Re-run Lighthouse to check
10. Repeat

Extracting critical CSS manually is especially painful — you need to identify every style rule that applies to above-the-fold content across every breakpoint.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not manually extract critical CSS or trace render-blocking resources through the network waterfall. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes for FCP

- **Inline critical CSS** — Extract styles needed for above-the-fold content and inline them in `<head>`. Defer the rest
- **Defer non-critical JavaScript** — Move scripts to the end of `<body>` or add `defer`/`async` attributes
- **Use [`font-display: swap`](https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display)** — Renders text immediately with a fallback font while the web font loads
- **Reduce server response time** — Use a CDN, enable server-side caching, and optimize database queries. Target TTFB under 200 ms
- **Eliminate redirect chains** — Each redirect adds 100–300 ms. Point links directly to the final URL
- **[Enable text compression](/lighthouse/how-to-fix-unminified-javascript/)** — Gzip or Brotli compression reduces transfer size, making HTML, CSS, and JS arrive faster
- **Preconnect to required origins** — [`<link rel="preconnect">`](https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/rel/preconnect) for third-party domains eliminates DNS and TLS handshake latency

## People Also Ask

### Is FCP the same as Time to First Byte?

No. TTFB measures when the first byte of the HTML response arrives at the browser. FCP measures when the first content actually paints on screen. TTFB is one component of FCP — the browser still needs to parse HTML, download blocking resources, and render content after TTFB.

### Can client-side rendering hurt FCP?

Yes. Single-page applications that rely entirely on client-side rendering show a blank page until JavaScript downloads, parses, and executes. Server-side rendering or static generation sends pre-rendered HTML, allowing FCP to happen before JavaScript loads.

### What is the relationship between FCP and CLS?

They measure different things. FCP measures time to first paint; [CLS](/lighthouse/how-to-fix-cumulative-layout-shift-cls/) measures visual stability. However, font loading issues can affect both: a slow font delays FCP, and a font swap after FCP can cause layout shifts.

### Does a CDN help FCP?

Yes. A CDN serves content from edge nodes close to the user, reducing TTFB by 50–200 ms in many cases. Less time waiting for HTML means faster FCP.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
