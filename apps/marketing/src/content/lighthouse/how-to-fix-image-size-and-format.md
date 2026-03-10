---
title: "How to Fix Image Size and Format Issues"
description: "Lighthouse flags images that are too large, incorrectly sized, or served in outdated formats. Learn how to optimize images for faster loading and better scores."
pubDate: 2026-03-10T00:00:00Z
auditId: "unsized-images"
category: "performance"
weight: 0
faq:
  - question: "What image formats does Lighthouse recommend?"
    answer: "Lighthouse recommends WebP and AVIF as modern image formats. WebP is 25-35% smaller than JPEG at similar quality. AVIF is 50% smaller than JPEG but has slower encoding times. Both are supported by all modern browsers."
  - question: "Should I set width and height on all images?"
    answer: "Yes. Explicit width and height attributes let the browser reserve the correct space before the image loads, preventing layout shifts. Use CSS for responsive sizing (max-width: 100%, height: auto) while keeping the HTML attributes for aspect ratio."
  - question: "What is the right image size for responsive design?"
    answer: "Serve images at 2x the display size for high-DPI screens. A hero image displayed at 600px wide needs to be 1200px wide. Use srcset and sizes attributes to let the browser choose the right size for each viewport and pixel density."
  - question: "Can Frontman fix image issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

Lighthouse flags several image-related issues:

- **"Image elements do not have explicit width and height"** — Missing dimensions cause [layout shifts](/lighthouse/how-to-fix-cumulative-layout-shift-cls/) when images load
- **"Properly size images"** — Images downloaded at a resolution much larger than their display size waste bandwidth
- **"Serve images in next-gen formats"** — JPEG and PNG files that could be significantly smaller as WebP or AVIF

These audits affect both Performance ([LCP](/lighthouse/how-to-fix-largest-contentful-paint-lcp/), [CLS](/lighthouse/how-to-fix-cumulative-layout-shift-cls/)) and overall page weight.

## Why Images Are Problematic

- **No explicit dimensions** — `<img src="photo.jpg">` without `width` and `height` gives the browser no information about the image's aspect ratio. The browser renders a 0×0 box, then reflows the layout when the image loads
- **Oversized images** — A 4000×3000 photo served for a 400×300 display area downloads 10× more pixels than needed
- **Outdated formats** — JPEG was designed in 1992. WebP (2010) and AVIF (2019) achieve better compression with equal or better quality
- **No responsive images** — Serving the same large image to mobile and desktop wastes bandwidth on mobile

## The Old Way to Fix It

1. Run Lighthouse and collect the list of flagged images
2. For each image, check its display size vs. intrinsic size using DevTools
3. Resize images to match their display size (at 2× for Retina)
4. Convert images to WebP or AVIF using tools like Squoosh, ImageMagick, or Sharp
5. Add `width` and `height` attributes to all `<img>` tags
6. Implement `srcset` and `sizes` for responsive images
7. Set up a `<picture>` element with format fallbacks
8. Re-run Lighthouse and check

Image optimization is repetitive and time-consuming — every image needs individual attention for resizing, format conversion, and responsive markup.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not manually resize images, convert formats, or add dimension attributes one by one. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Always set `width` and `height`** — Even for responsive images. The browser uses these to calculate the aspect ratio and reserve space. Add `style="max-width: 100%; height: auto"` for responsive behavior
- **Use `srcset` and `sizes`** — Let the browser choose the right image size: `<img srcset="photo-400.webp 400w, photo-800.webp 800w, photo-1200.webp 1200w" sizes="(max-width: 600px) 100vw, 50vw">`
- **Serve WebP with JPEG fallback** — Use `<picture>` for format switching: `<picture><source srcset="photo.webp" type="image/webp"><img src="photo.jpg"></picture>`
- **Use framework image components** — `next/image` (Next.js), `astro:image` (Astro), and `@nuxt/image` handle resizing, format conversion, and responsive attributes automatically
- **Compress aggressively** — Quality 75-80 for JPEG/WebP is nearly indistinguishable from 100 to the human eye but 40-50% smaller
- **Lazy load below-the-fold images** — `loading="lazy"` defers off-screen images. Never lazy-load the [LCP element](/lighthouse/how-to-fix-largest-contentful-paint-lcp/)

## People Also Ask

### Should I use WebP or AVIF?

Use both. AVIF offers better compression (50% smaller than JPEG) but encoding is slower and browser support is slightly behind WebP. Serve AVIF with WebP and JPEG fallbacks using `<picture>`: AVIF for browsers that support it, WebP for those that don't, and JPEG as the final fallback.

### Do CSS background images need optimization?

Yes. CSS `background-image` files are not covered by `srcset`, so you need to handle responsive sizing with media queries or the `image-set()` function. Consider switching decorative background images to `<img>` elements with proper responsive attributes.

### Does image CDN processing replace manual optimization?

Image CDNs like Cloudinary, imgix, or Cloudflare Images resize and convert images on the fly via URL parameters. They handle format negotiation (serving WebP/AVIF based on the browser's Accept header), responsive sizing, and caching. They eliminate most manual optimization work.

### What about SVGs?

SVGs are resolution-independent and typically small. Lighthouse does not flag SVGs for size or format issues. Use SVGs for icons, logos, and illustrations. Optimize them with SVGO to remove metadata and unnecessary attributes.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
