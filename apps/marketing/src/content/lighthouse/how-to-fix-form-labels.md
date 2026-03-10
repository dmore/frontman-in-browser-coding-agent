---
title: "How to Fix Form Elements Without Labels"
description: "Lighthouse flags form inputs that screen readers cannot identify. Learn how to associate labels with every form element for accessibility and usability."
pubDate: 2026-03-10T00:00:00Z
auditId: "label"
category: "accessibility"
weight: 10
faq:
  - question: "What counts as a form label?"
    answer: "A form label is an associated <label> element, an aria-label attribute, an aria-labelledby reference, or a title attribute that identifies the purpose of an input field. The <label> element with a matching for/id pair is the most reliable and accessible method."
  - question: "Does the placeholder attribute count as a label?"
    answer: "No. The placeholder attribute is not a substitute for a label. Placeholder text disappears when the user starts typing, leaving them with no indication of what the field is for. Screen readers may or may not announce placeholder text depending on the browser."
  - question: "Do hidden inputs need labels?"
    answer: "No. Input elements with type='hidden' are not visible to users or screen readers and do not need labels. The audit only applies to visible, interactive form controls."
  - question: "Can Frontman fix form label issues automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "Form elements do not have associated labels," it means one or more input fields, select elements, or textareas have no accessible label. This is a **weight-10 accessibility audit** — the highest severity. Screen reader users encounter these fields with no indication of what information to enter.

Lighthouse checks for `<label>` elements linked via `for`/`id`, wrapping `<label>` elements, `aria-label`, `aria-labelledby`, and `title` attributes.

## Why Form Elements Are Missing Labels

- **Placeholder-only inputs** — Designers use `placeholder` instead of visible labels, thinking it looks cleaner
- **Missing `for` attribute** — A `<label>` exists near the input but is not programmatically associated via `for`/`id`
- **Custom form components** — React/Vue/Svelte components that render inputs without passing through label associations
- **Icon-prefixed inputs** — Search fields with a magnifying glass icon but no text label
- **Dynamically generated forms** — Forms built from JSON schemas or CMS data that do not include label configuration

## The Old Way to Fix It

1. Run Lighthouse or an accessibility scanner
2. For each flagged input, determine what information it collects
3. Add a `<label for="field-id">` with descriptive text
4. If a visible label is not desired, use `aria-label` or visually hidden text
5. Verify the association works by clicking the label — the input should receive focus
6. Re-run the audit

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. Because Frontman sees the form in the browser, it understands each field's purpose from placeholder text, surrounding copy, and field names — and adds the right label type. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Use `<label>` with `for` attribute** — The most reliable method: `<label for="email">Email</label> <input id="email" type="email">`
- **Or wrap the input in a `<label>`** — `<label>Email <input type="email"></label>` — the association is implicit
- **Use `aria-label` for visually hidden labels** — `<input type="search" aria-label="Search products">` when you need a label without visible text
- **Use `aria-labelledby` for complex labels** — When the label text comes from another element: `<input aria-labelledby="price-label currency-label">`
- **Never rely on placeholder alone** — Placeholder text is a hint, not a label. It disappears on input and is not reliably announced
- **Add visible labels whenever possible** — Visible labels help everyone, not just screen reader users. They clarify what each field expects and persist while the user types

## People Also Ask

### Can I use `title` as a form label?

The `title` attribute is recognized as a label by Lighthouse, but it is the lowest priority naming method. It shows as a tooltip on hover and is not visible on touch devices. Use `<label>` or `aria-label` instead. Reserve `title` for supplementary hints.

### Do radio buttons and checkboxes need labels?

Yes. Each radio button and checkbox needs its own `<label>`. Additionally, a group of radio buttons should be wrapped in a `<fieldset>` with a `<legend>` to describe the group. Example: `<fieldset><legend>Shipping method</legend>...radios...</fieldset>`.

### How do I label a select element?

Use the same `<label for="id">` pattern as text inputs: `<label for="country">Country</label> <select id="country">...</select>`. The `<select>` element must have an `id` that matches the `<label>`'s `for` attribute.

### What about custom dropdown components?

Custom dropdowns built with `<div>` elements need ARIA attributes: `role="combobox"` or `role="listbox"`, `aria-label` or `aria-labelledby`, and proper `aria-expanded` and `aria-activedescendant` states. Native `<select>` elements are more accessible by default.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
