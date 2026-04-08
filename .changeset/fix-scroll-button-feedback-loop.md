---
"@frontman-ai/client": patch
---

fix: move ScrollButton outside contentRef to break ResizeObserver feedback loop

The scroll-to-bottom button was rendered inside the ResizeObserver-watched div.
Its 32px show/hide cycle (driven by `isAtBottom`) caused the ResizeObserver to
snap scroll position, which toggled `isAtBottom`, which toggled the button —
creating an infinite oscillation that made it impossible to scroll up.
