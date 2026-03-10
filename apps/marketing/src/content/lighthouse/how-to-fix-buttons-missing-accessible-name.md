---
title: "How to Fix Buttons Without an Accessible Name"
description: "Lighthouse flags buttons that screen readers cannot identify. Learn how to add accessible names to buttons so all users can interact with your interface."
pubDate: 2026-03-10T00:00:00Z
auditId: "button-name"
category: "accessibility"
weight: 10
faq:
  - question: "What is an accessible name for a button?"
    answer: "An accessible name is the text that screen readers announce when a user focuses on a button. It can come from visible text content, an aria-label attribute, an aria-labelledby reference, or a title attribute (in that priority order)."
  - question: "Why do icon buttons fail this audit?"
    answer: "Icon-only buttons (like a hamburger menu icon or a close X) contain no text content. Without text, aria-label, or aria-labelledby, the screen reader announces just 'button' with no indication of what the button does."
  - question: "Should I use aria-label or visible text?"
    answer: "Visible text is always preferred because all users — sighted and non-sighted — benefit from it. Use aria-label only when the button's visual design makes visible text impractical (icon-only buttons, close buttons, etc.)."
  - question: "Can Frontman fix button accessibility issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Buttons do not have an accessible name," it means one or more [`<button>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button) elements (or elements with `role="button"`) have no text that screen readers can announce. This is a **weight-10 accessibility audit** — the highest severity. A screen reader user encounters these buttons as just "button" with no indication of what they do.

## Why Buttons Are Missing Names

- **Icon-only buttons** — Buttons that contain only an SVG icon or an icon font character with no text
- **Empty buttons** — `<button></button>` with no content, often used as close buttons or toggles styled with CSS
- **Image buttons without alt** — Buttons that contain an `<img>` without alt text
- **CSS content as label** — Buttons that show their label via CSS `::before` or `::after` pseudo-elements, which are not reliably announced by all screen readers
- **Dynamically generated content** — Buttons whose text content is injected by JavaScript after the accessibility tree is built

## The Old Way to Fix It

1. Run Lighthouse or an accessibility scanner
2. For each flagged button, determine what the button does
3. Decide whether to add visible text or aria-label
4. Find the component in your source code
5. Add the appropriate label:
   - Visible text: `<button>Close menu</button>`
   - Aria-label: `<button aria-label="Close menu"><CloseIcon /></button>`
   - For image buttons: add alt text to the contained image
6. Re-run the audit to verify

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. Because Frontman sees each button in the rendered page, it understands the button's purpose from its icon and position — and writes accurate labels, not generic ones. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Add visible text when possible** — `<button><SearchIcon /> Search</button>` is better than an icon-only button because all users can read the label
- **Use [`aria-label`](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Attributes/aria-label) for icon-only buttons** — `<button aria-label="Close"><CloseIcon /></button>` gives screen readers a name without visible text
- **Use `aria-labelledby`** for complex buttons — When the label text exists elsewhere on the page, point to it: `<button aria-labelledby="cart-count">` where `<span id="cart-count">3 items in cart</span>`
- **Add alt text to image buttons** — `<button><img src="search.svg" alt="Search"></button>`
- **Use visually hidden text** as an alternative to aria-label — `<button><span class="sr-only">Close menu</span><CloseIcon /></button>` works with screen readers and translation tools
- **Test with a screen reader** — VoiceOver (macOS), NVDA (Windows), or ChromeVox to verify buttons are announced correctly

## People Also Ask

### What is the difference between aria-label and aria-labelledby?

`aria-label` provides the label text directly as a string: `aria-label="Close"`. `aria-labelledby` points to another element's ID whose text content becomes the label: `aria-labelledby="heading-1"`. Use `aria-labelledby` when the label text already exists in the DOM.

### Do submit buttons need accessible names?

Yes. `<input type="submit">` uses the `value` attribute as its accessible name. `<button type="submit">` uses its text content. If neither is present, the button has no accessible name. Make sure every submit button has a clear label like "Submit form" or "Create account."

### Does this audit apply to links styled as buttons?

No. [Links](/lighthouse/how-to-fix-links-missing-discernible-name/) have their own Lighthouse audit (`link-name`). This audit specifically covers `<button>` elements and elements with `role="button"`. Both need accessible names, but they are checked by separate audits.

### Can CSS `content` provide an accessible name?

CSS `::before` and `::after` content is included in the accessibility tree by most modern browsers, but support varies. Relying on CSS content for accessible names is fragile. Use real text content, `aria-label`, or visually hidden text instead.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
