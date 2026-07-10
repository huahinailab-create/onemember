# 12 — Conversion Funnel

> The website's job doesn't end at signup — it ends at a **Launch Ready merchant who upgrades and refers**. The funnel below welds the website to the product journey (MR-001–003) and the sales system (SALES-001). Each stage: goal, what the visitor sees, drop-off risks, countermeasures, metric.

```
Visitor → Learn → Trust → Start Free → Launch Ready → Upgrade → Refer
```

## Stage 1 — Visitor (arrive)

- **Sources:** street QR (poster footer "Powered by OneMember"), LINE/FB shares, sales follow-up links, organic search (industries pages), direct.
- **Sees:** Home hero — the promise in 5 seconds, in Thai, on a phone.
- **Drop-off risks:** slow load on 4G · English-first page for a Thai visitor · vague hero ("empowering commerce…").
- **Counters:** <1.5s hero paint (13) · geo/browser language default · concrete hero (10 minutes, 100 free, no app).
- **Metric:** bounce rate; scroll-past-hero %.

## Stage 2 — Learn (understand it fits *my* shop)

- **Sees:** problem/solution scroll → industry page (the "it's for shops like mine" click) → feature outcomes → 90-second video.
- **Drop-off risks:** feature-speak ("campaign engine") · can't find their industry · doubt about POS/staff/effort.
- **Counters:** outcome copy rule (04) · 10 industries + "different shop?" line (05) · FAQ teaser answering the top-4 doubts right on Home.
- **Metric:** industry-page visit rate; video completion; pages/session.

## Stage 3 — Trust (believe it's safe to try)

- **Sees:** real screenshots, honest comparison table, real testimonials (never fake), transparent pricing with real Free, PDPA/security statements, LINE chat = a human is reachable.
- **Drop-off risks:** zero social proof pre-pilot · price anxiety · "another startup that will vanish" · data-ownership fear.
- **Counters:** pilot testimonials shipped ASAP (section hidden till real — 03 §8) · "free means free / no card" microcopy at every CTA · About-page founder story + "your data is yours, export anytime" repeated · LINE reply-time promise kept.
- **Metric:** pricing-page → Start Free CTR; LINE chat starts; FAQ engagement.

## Stage 4 — Start Free (the website's classic "conversion")

- **Sees:** register at app.onemember.co — minimal fields, Thai, mobile-perfect (exists).
- **Drop-off risks:** context break website→app (different look) · form friction · signing up on desktop at work but shop phone elsewhere.
- **Counters:** visual continuity (same wordmark/palette — already true) · keep registration to essentials · post-signup email/LINE with "continue on your phone" link.
- **Metric — NORTH STAR:** unique visitors → Start Free %; by source (street QR vs organic vs social vs sales-assisted).

## Stage 5 — Launch Ready (the *real* conversion)

- **Sees:** the product's guided journey — onboarding wizard → launch checklist → next-action → 🎉 (all built; the website must have set honest expectations so this feels *exactly* as promised).
- **Drop-off risks:** signup-and-stall (no poster printed) · solo merchants without sales assist stall at logo/product steps.
- **Counters:** product already nudges (checklist, step-success, trial emails) · site-side: "your first 10 minutes" follow-up sequence linking checklist deep-links · sales cadence A for assisted signups (SALES-001 §08).
- **Metric:** Start Free → Launch Ready ≤7 days ≥70% (shared KPI with sales); web-attributed vs sales-assisted split.

## Stage 6 — Upgrade (pay)

- **Sees:** trial-ending flow (built) · 100-member wall as a *celebration* (product) · pricing page revisit — must answer "what do I lose/keep" instantly (06 FAQ).
- **Drop-off risks:** silent trial expiry · fear of losing data · unclear tier fit.
- **Counters:** "keep everything, choose your pace" messaging · day-21 human call for assisted accounts · Free tier framed as honorable choice (they remain future upgrades + referrers).
- **Metric:** trial → paid ≥25%; Free → paid ≥10%/quarter of eligible (SALES-001 §10).

## Stage 7 — Refer (the loop)

- **Sees:** their own QR poster footer working on *their* customers' friends · "which shop owner do you know?" ask at success touches · (future) referral incentive page.
- **Drop-off risks:** referral asked before value felt · no easy share artifact.
- **Counters:** ask at success moments only (week-4/6 wins) · shareable one-tap "recommend OneMember" LINE message with their shop's story · Founding Merchant plaque = physical conversation starter.
- **Metric:** ≥25% of new trials referral-sourced by merchant #100 (GTM-001).

---

## Funnel math + instrumentation stance

Illustrative early targets (recalibrate with 90 days of data): 1,000 visitors → 35% reach pricing/industry depth → **4–6% Start Free** (Thai SME SaaS with street-brand support) → 70% Launch Ready → 25% paid + Free-active pool → ≥25% referral share compounding acquisition.

Instrumentation (implementation later, design now): UTM discipline on every poster/LINE/sales link · one analytics property spanning website + app signup handoff · funnel events: hero_view, industry_view, pricing_view, video_play, line_click, start_free_click, register_complete, launch_ready (product event, joined by account id) · weekly funnel review alongside the sales KPI ritual (SALES-001 §10).

**Two golden rules:** (1) never optimize stage 4 at the cost of stage 5 — overselling creates signups that die at onboarding; (2) every website promise must be provably true in the product within 10 minutes.
