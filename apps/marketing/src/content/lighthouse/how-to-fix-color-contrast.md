---
title: "How to Fix Color Contrast Issues"
description: "Lighthouse flags text whose foreground and background colors do not have enough contrast for readability. Learn how to meet WCAG contrast requirements."
pubDate: 2026-03-10T00:00:00Z
auditId: "color-contrast"
category: "accessibility"
weight: 7
faq:
  - question: "What contrast ratio does WCAG require?"
    answer: "WCAG 2.1 Level AA requires a contrast ratio of at least 4.5:1 for normal text and 3:1 for large text (18pt/24px regular or 14pt/18.66px bold). Level AAA requires 7:1 for normal text and 4.5:1 for large text."
  - question: "How do I check contrast ratios?"
    answer: "Chrome DevTools shows contrast ratios in the color picker (click any color in the Styles panel). The Accessibility panel also flags contrast issues. Online tools like WebAIM Contrast Checker and Coolors Contrast Checker calculate ratios for any two colors."
  - question: "Do disabled elements need to pass contrast?"
    answer: "WCAG does not require disabled or inactive UI components to meet contrast requirements. However, users still need to recognize that a control exists and is disabled, so low-contrast disabled states should still be distinguishable."
  - question: "Can Frontman fix contrast issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Background and foreground colors do not have a sufficient contrast ratio," it means text on the page is hard to read because the color difference between the text and its background is too low. This is a **weight-7 accessibility audit** that directly affects readability for everyone — not just users with vision impairments.

Lighthouse tests every text element on the page and reports those that fail the WCAG 2.1 Level AA contrast ratio thresholds.

## Why Contrast Matters

- **1 in 12 men and 1 in 200 women** have some form of color vision deficiency
- **Low contrast** is the most common accessibility barrier on the web — it affects users with low vision, aging eyes, bright screens, and outdoor mobile use
- **WCAG compliance** — Level AA contrast requirements are a legal requirement in many jurisdictions under the ADA (US), EN 301 549 (EU), and similar laws
- **Readability** — Even users with perfect vision read low-contrast text slower and with more errors

## The Old Way to Fix It

1. Run Lighthouse or an accessibility scanner
2. For each flagged element, note the current foreground and background colors
3. Use a contrast checker (WebAIM, Chrome DevTools color picker) to find the current ratio
4. Adjust either the foreground or background color until the ratio reaches 4.5:1
5. Check that the adjusted color still fits the design system
6. Update the color in CSS or the design token
7. Re-run Lighthouse for each page where the color appears
8. Repeat for every flagged element

The challenge is balancing accessibility requirements with design aesthetics. Designers often resist darkening light grays or lightening dark backgrounds because it changes the visual feel.

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. Because Frontman sees the rendered page, it knows the actual computed colors — not just what is in the stylesheet. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Increase text darkness on light backgrounds** — Move from light grays (#9ca3af) to medium grays (#6b7280 or darker) for body text
- **Increase text lightness on dark backgrounds** — Move from dark grays (#71717a) to lighter grays (#a1a1aa or lighter) for text on dark backgrounds
- **Use design tokens** — Define accessible color pairs in your design system so every component inherits the correct contrast. Fix the token once, fix every usage
- **Check large text separately** — Text at 18pt (24px) or larger, or 14pt (18.66px) bold, only needs a 3:1 ratio. Large headings have more flexibility
- **Test with real backgrounds** — A color might pass contrast on a solid background but fail on a gradient, image, or semi-transparent overlay
- **Add background overlays** — For text on images, add a semi-transparent dark overlay behind the text to guarantee contrast regardless of the image content

## People Also Ask

### What is a contrast ratio?

The contrast ratio is the difference in luminance between two colors, expressed as a ratio from 1:1 (identical colors) to 21:1 (black on white). It is calculated using the [relative luminance](https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html) formula from WCAG 2.1.

### Does color contrast apply to images of text?

Yes. WCAG 1.4.3 applies to images that contain text (like banners or infographics with embedded text). The same 4.5:1 ratio applies. The fix is either adjusting the image or replacing the image text with real HTML text.

### How do I handle contrast with semi-transparent backgrounds?

Calculate the effective background color by compositing the semi-transparent color against the underlying background. If the underlying background varies (like an image), test against the lightest and darkest areas. Tools like the Chrome DevTools color picker handle alpha compositing automatically.

### Do icons need to meet contrast requirements?

Icons that convey meaning (like a warning icon or a navigation icon without text) need to meet the 3:1 non-text contrast ratio from WCAG 2.1 Level AA (guideline 1.4.11). Decorative icons that accompany text do not need to meet contrast requirements independently.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
