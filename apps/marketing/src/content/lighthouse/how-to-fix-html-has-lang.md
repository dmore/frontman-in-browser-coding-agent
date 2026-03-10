---
title: "How to Fix Missing HTML Lang Attribute"
description: "Lighthouse flags pages where the html element is missing a lang attribute. Learn how to set the document language for screen readers and search engines."
pubDate: 2026-03-10T00:00:00Z
auditId: "html-has-lang"
category: "accessibility"
weight: 7
faq:
  - question: "What is the html lang attribute?"
    answer: "The lang attribute on the <html> element declares the primary language of the page content. It uses BCP 47 language tags like 'en' for English, 'fr' for French, 'es' for Spanish, or 'en-US' for American English."
  - question: "Does the lang attribute affect SEO?"
    answer: "The lang attribute is not a direct ranking signal, but it helps search engines understand the page language for serving it to the right audience. Combined with hreflang tags, it supports international SEO."
  - question: "Do I need lang attributes on individual elements?"
    answer: "Only when an element's content is in a different language than the page. For example, a French quote on an English page: <blockquote lang='fr'>Bonjour</blockquote>. The page-level lang on <html> covers all other content."
  - question: "Can Frontman fix the lang attribute automatically?"
    answer: "Yes. Tell Frontman to fix your Lighthouse issues and it handles the rest. It runs the audit, fixes the code, and re-runs the audit to check the score improved — iterating until the metrics pass."
---

## What Lighthouse Is Telling You

When Lighthouse flags "`<html>` element does not have a `[lang]` attribute," the page is missing the language declaration that screen readers and search engines need. This is a **weight-7 accessibility audit**. Without it, screen readers cannot select the correct pronunciation engine, causing garbled speech output for non-English content.

## Why the Lang Attribute Matters

- **Screen reader pronunciation** — Screen readers switch speech synthesis engines based on the `lang` attribute. Without it, a French page is read with English pronunciation rules, making it incomprehensible
- **Translation tools** — Browser auto-translate features and services like Google Translate use the `lang` attribute to determine the source language
- **Search engines** — While not a direct ranking signal, the `lang` attribute helps search engines categorize content for language-specific search results
- **CSS language selectors** — CSS selectors like `:lang(en)` and `:lang(fr)` let you apply language-specific styling (e.g., different quotation marks)

## The Old Way to Fix It

1. Run Lighthouse and see the `html-has-lang` audit fail
2. Determine the primary language of the page
3. Open the HTML template or layout file
4. Add `lang="en"` (or the appropriate language code) to the `<html>` element
5. For multi-language sites, ensure the lang attribute is set dynamically based on the page's language
6. Re-run Lighthouse to verify

## The Frontman Way

Tell Frontman to fix your Lighthouse issues. That is the entire workflow.

Frontman has a built-in Lighthouse tool. It runs the audit, reads the failing scores, fixes the underlying code, and re-runs the audit to verify the score went up. If issues remain, it keeps going — iterating through fixes and re-checks until the metrics pass. You do not search through layout files to find the `<html>` tag. You say "fix the Lighthouse issues on this page" and Frontman handles the rest.

## Key Fixes

- **Add `lang` to the `<html>` element** — `<html lang="en">` for English content. Use the correct [BCP 47 language tag](https://www.ietf.org/rfc/bcp/bcp47.txt) for your language
- **Set it in your root layout** — In Next.js (`layout.tsx`), Astro (`Layout.astro`), or any framework, the `<html>` tag in the root layout is where `lang` belongs
- **Use region subtags when relevant** — `en-US` vs. `en-GB` matters for screen reader pronunciation of dates, currency, and spelling differences
- **Handle multilingual sites dynamically** — For i18n sites, set `lang` based on the current locale: `<html lang={locale}>`
- **Add `lang` to inline content in other languages** — `<span lang="ja">日本語</span>` on an English page tells the screen reader to switch to Japanese pronunciation for that span

## People Also Ask

### What language code should I use?

Use [BCP 47 language tags](https://www.ietf.org/rfc/bcp/bcp47.txt). Common examples: `en` (English), `es` (Spanish), `fr` (French), `de` (German), `ja` (Japanese), `zh-Hans` (Simplified Chinese), `pt-BR` (Brazilian Portuguese). The short form (`en`) is fine for most cases; add the region (`en-US`) when the distinction matters.

### What happens if I set the wrong language?

Screen readers will use the wrong pronunciation engine. An English page marked as `lang="fr"` will be read with French pronunciation, making words sound incorrect. Search engines may also serve the page to the wrong language audience.

### Is `html-has-lang` the same as `html-lang-valid`?

No. `html-has-lang` checks that the attribute exists. `html-lang-valid` checks that the value is a valid BCP 47 language tag. You can fail the second audit with `lang="english"` (invalid — should be `lang="en"`). Both need to pass.

### How does this relate to the `hreflang` tag?

The `lang` attribute declares the current page's language. `hreflang` (via `<link rel="alternate" hreflang="...">`) tells search engines about alternate language versions of the same content. They serve different purposes: `lang` is for the current page, `hreflang` is for cross-language linking.

---

You can use [Frontman](https://frontman.sh) to automatically fix this and any other Lighthouse issue. Frontman runs the audit, reads the results, applies the fixes, and verifies the improvement — all inside the browser you are already working in. [Get started with one install command](https://frontman.sh/blog/getting-started/).
