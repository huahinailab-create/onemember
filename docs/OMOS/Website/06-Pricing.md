# 06 — Pricing Page

> Philosophy: **sell the growth stage, not the price tag.** Each tier is named after where the shop *is*, and the page reads as a growth story. Amounts stay `[฿ DECISION-014]` placeholders — the layout must work whatever the PO ratifies. No pressure mechanics, ever: no fake discounts, no countdowns.

## Page structure

### Hero
- **Headline:** *Start free. Pay when you're growing.*
- **Sub-headline:** Every plan includes the poster, the two-tap counter, and Thai support. You only ever pay for the size of your success.
- Toggle: Monthly / Yearly (yearly = "2 months free" framing when pricing allows).

### The four tiers (cards, Professional visually highlighted "Most popular")

| | **Free** | **Starter** | **Professional** | **Enterprise** |
|---|---|---|---|---|
| Tagline | *Prove it works* | *Your regulars, on autopilot* | *Run it like a chain* | *Your brand, your network* |
| For whom | Every shop's first step | Single shops past their first 100 regulars | Serious shops & busy counters | Chains, franchises, brands |
| Price | ฿0 forever | `[฿ DECISION-014]`/mo | `[฿ DECISION-014]`/mo | Talk to us |
| The story line | "Better than paper — free, forever, up to 100 members." | "You have 100+ regulars now. Keep them coming." | "Every tool: campaigns, win-back, storefront, insights." | "Multi-branch, white-label, corporate controls." |
| CTA | Start Free | Start Free (trial) | **Start Free (trial)** | Talk to us |

Under cards: *"Every new shop starts with 30 days of Professional — free, no card. Keep what you love; drop to Free any time. Your members and data stay yours on every plan, forever."*

### Value framing block (before the table — the ROI moment)
- **Headline:** *What does one returning regular pay for?*
- **Body:** the break-even math from [SALES-001 §07](../Sales/07-ROI-Calculator.md), simplified: "A ฿120 average ticket means about **6 extra visits a month covers Starter** — from all your members combined. Most shops see that in week one." → interactive ROI calculator embed `[BUILD ITEM — GTM-001 §11]`, with a static example until built.

### Comparison table (honest, complete)

Rows grouped by outcome, not by module: **Get members** (poster & Launch Kit, member limit 100/‑/‑/custom, join page) · **Reward visits** (campaigns count, stamps+points, birthday automation) · **Bring them back** (win-back alerts, insights) · **Sell more** (storefront, direct orders) · **Run the counter** (Counter Mode, staff guide) · **Grow bigger** (branches, white-label, roles & permissions, priority support, exports/API). Cells honest: Free row genuinely useful; Enterprise cells "custom". Exact limits map to `config/subscriptions.php` grid once DECISION-013/014 are ratified.

### Enterprise band
- **Headline:** *For brands with bigger plans.*
- Three pillars: **Chain stores** — every branch on one dashboard, campaigns set centrally, run locally. **White-label** — your logo, your colors, your store URL on every customer touchpoint. **Corporate controls** — roles, audit trails, exports, a named human who answers.
- CTA: `Talk to us` → contact?type=enterprise. No prices, no self-serve.

### Pricing FAQ (10 — pulled from the [FAQ master](./08-FAQ.md))
Is Free really free? · What happens at 100 members? · What happens when my trial ends? · Can I cancel anytime? · Do you take a % of my sales? (No — never.) · Card required? (No.) · What payment methods? · Can I change plans? · What happens to my data if I stop paying? (It waits for you; export anytime.) · Do prices include VAT?

### Final CTA
*The most expensive loyalty plan is the customers you lose this month.* → `Start Free`.
