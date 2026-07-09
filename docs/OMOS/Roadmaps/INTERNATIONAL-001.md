# INTERNATIONAL-001 — Thailand + Myanmar Readiness Blueprint

| Field | Value |
|---|---|
| **Document type** | Architecture & product planning blueprint (no implementation) |
| **Sprint** | INTERNATIONAL-001 |
| **Status** | 📋 Blueprint — awaiting CTO/PO ratification |
| **Author** | Claude Fable 5 (Solution Architect role) |
| **Date** | 2026-07-10 |
| **Inputs** | MR-004 Merchant Readiness Audit (Part 5 findings), BETA-008B localization model, ADR-011 (money never touches OneMember), ADR-012 (Country Extensions, Layer 3), config/countries.php, config/localization.php |
| **Related** | [09-Roadmap/Long-term-Roadmap.md](../09-Roadmap/Long-term-Roadmap.md), [Sprints/MERCHANT-READY-001.md](../Sprints/MERCHANT-READY-001.md) |

**Rule of this document:** every recommendation is a *plan*, not a change. Nothing here modifies Laravel, schema, routes, or behaviour. Implementation happens only in future, individually approved sprints.

---

## Executive Summary

OneMember is architecturally ready for Thailand today and structurally prepared — but not content- or format-ready — for Myanmar. The platform's strongest international assets are decisions already made: money never touches OneMember (ADR-011), locale is config-driven with English fallback (BETA-008B), country choice is a first-class merchant field, and the storefront payment model (merchant-uploaded QR) is provider-agnostic. The gaps are concentrated in four areas: **address structure** (a single free-text field today), **per-currency formatting** (hardcoded 2 decimals), **Burmese language + typography** (placeholder locale, Latin-only webfont, Zawgyi encoding risk), and **payment/billing localization** (Stripe-only billing, THB/card-centric).

The recommended path: **Phase 1** hardens Thailand (the live market) with zero-risk polish; **Phase 2** builds the country-profile foundations (address schema, currency formatting rules, font self-hosting) that every future market needs; **Phase 3** activates Myanmar specifically (Burmese translation, Zawgyi mitigation, MMK, local payment rails) once there is a business commitment. Nothing in Phase 2 is Myanmar-specific — it is the reusable machinery for Laos, Cambodia, Vietnam, Malaysia and Singapore afterwards.

---

## 1. Country Strategy

### Priority ranking (implementation order)

| # | Country | Rationale | Readiness today |
|---|---|---|---|
| 1 | 🇹🇭 **Thailand** | Live launch market; full TH UI; THB; pilot merchants | ✅ Ready (polish only) |
| 2 | 🇲🇲 **Myanmar** | CTO-designated second market; large SME base underserved by loyalty SaaS; Thai-Myanmar trade corridor familiarity | ⚠️ Partial (this blueprint) |
| 3 | 🇱🇦 **Laos** | Lowest incremental cost after TH: UTC+7, LAK already listed, Lao customer-language slot exists, Thai-adjacent commerce habits; small market → low revenue priority | 🔲 Structural only |
| 4 | 🇰🇭 **Cambodia** | KHR + USD dual-currency already modelled in BETA-008B tests; USD-heavy economy simplifies pricing; Khmer script needs font work like Burmese | 🔲 Structural only |
| 5 | 🇻🇳 **Vietnam** | Big market, but competitive loyalty space, VND formatting (no decimals, large magnitudes), and Vietnamese-first UI expectation → needs full translation before entry | 🔲 Structural only |
| 6 | 🇲🇾 **Malaysia** | English-workable market lowers translation cost; strong card/eWallet rails (good Stripe coverage); enters when billing matures | 🔲 Structural only |
| 7 | 🇸🇬 **Singapore** | Smallest SME count but highest willingness-to-pay; English UI ready today; best served after MY (shared rails, PayNow≈PromptPay analogue) | 🔲 Structural only |

**Principle:** rank 3–7 are deliberately *not* scheduled. Phase 2's country-profile machinery must make adding each of them a **content + configuration** exercise, never an engineering project.

---

## 2. Address Architecture

**Today:** `merchants.address` is one nullable free-text column; members have only optional `postal_code`. That is honest for a loyalty product (addresses are used for print materials and analytics, not logistics) — but Commerce delivery orders capture a free-text address too, and that is where structure will eventually matter.

**Proposed model (future):** a per-country **address profile** (config-driven, like `config/countries.php`) defining ordered fields, labels, and requiredness. No new tables — structured parts can live in existing JSON `settings`/order payloads when implemented.

### Thailand 🇹🇭

| Field | Required | Notes |
|---|---|---|
| Address line (house no., street, village/moo) | ✅ | Free text |
| Subdistrict (ตำบล/แขวง — tambon/khwaeng) | ✅ | Label switches to แขวง inside Bangkok |
| District (อำเภอ/เขต — amphoe/khet) | ✅ | Label switches to เขต inside Bangkok |
| Province (จังหวัด) | ✅ | 77 provinces; closed list |
| Postal code | ✅ | 5 digits; deterministic from subdistrict |
| Country | ✅ | Defaulted |

### Myanmar 🇲🇲

| Field | Required | Notes |
|---|---|---|
| Address line (house no., street, quarter/ward) | ✅ | Free text |
| **Township (မြို့နယ်)** | ✅ | **Replaces District as the primary admin unit** — this is the unit people actually use (e.g. "Hlaing Township, Yangon") |
| City/Town | Optional | Often implied by township |
| State / Region (ပြည်နယ်/တိုင်းဒေသကြီး) | ✅ | 7 states + 7 regions + NPT union territory; closed list |
| Postal code | **Optional → hidden** | Myanmar has a postcode system on paper but it is **not used in daily life**. **Recommendation: do not render the postcode field at all for MM** (see rule below) |
| Country | ✅ | Defaulted |

### The two rules the profile system must support

1. **"When does postcode disappear?"** — Postcode is per-country: `required` (TH, VN, MY, SG), `optional` (KH, LA), or `hidden` (MM). A hidden field is not rendered and never validated — an absent-by-design field, not an empty one.
2. **"When does Township replace District?"** — The *level-2 admin unit* is a per-country label + semantics: TH = District (อำเภอ/เขต) under Province; MM = **Township** under State/Region (and there is no subdistrict tier). The profile defines tier count, order, labels (localized), and which tiers come from closed lists.

### Future countries (for the same profile system)

| Country | Tiers | Postcode |
|---|---|---|
| Laos | Province → District → Village | Optional (rarely used) |
| Cambodia | Province → District (srok/khan) → Commune | Optional |
| Vietnam | Province/City → District → Ward | Required (being reformed — track) |
| Malaysia | State → City → Postcode | Required (5-digit) |
| Singapore | Postcode (+unit) is king — 6-digit code identifies the building | Required, primary lookup key |

**No implementation now.** When implemented: config-first, JSON storage, no migration until a real feature (e.g. delivery zones) needs queryable columns.

---

## 3. Language Strategy

Current model (BETA-008B) is correct and scales: **merchant internal language** (full-quality UI locales only: en, th) is separate from **customer-offered languages** (any of 15; English fallback until translated).

| Surface | Thailand | Myanmar | Policy |
|---|---|---|---|
| **Merchant UI** | Thai ✅ (shipped, completeness-tested) | **Burmese required before launch** — Myanmar SME owners cannot be assumed to work in English; translate `lang/my/` fully + add to `internal_languages` + extend `TranslationCompletenessTest` | A merchant UI language ships only at 100% coverage — no partial merchant locales, ever |
| **Customer UI** (storefront, join, portal, order) | Thai + English ✅ | Burmese — translate the ~small customer-surface string set **first** (it is 10× smaller than merchant UI; highest value per translated string) | Customer surfaces may offer any language; fallback keeps them functional |
| **Documentation / Knowledge Center** | 6 TH articles + EN fallback today → grow TH coverage of the top-10 articles | Burmese "Getting Started" set (≈6 articles, mirroring the Thai approach) at launch; EN fallback for the long tail | Rails already support locale + fallback (PLATFORM-002 P7) |
| **Support** | Thai-language support required (founder-led today) | Burmese-speaking support agent or partner **required at launch** — do not enter without it; English support does not work for this segment | Support language is a launch gate, not an afterthought |

**Sequence recommendation for Myanmar:** customer surfaces → Getting Started articles → merchant UI → full Knowledge Center. Each step is shippable alone.

---

## 4. Currency Strategy

Anchor: **ADR-011 — OneMember never converts, settles, or touches customer money.** Two money streams exist and must never be conflated:

### A. Merchant billing currency (what merchants pay OneMember)

| Market | Recommendation |
|---|---|
| Thailand | **THB** via Stripe (current path). Publish THB prices; never show converted amounts |
| Myanmar | **Do not attempt MMK billing at launch.** Stripe has no Myanmar acquiring; card penetration is low. Options in order of preference: (1) **USD billing** for the minority with cards, (2) **prepaid top-up via local partner / manual bank transfer** with an internal ledger — an operational process, not a payments integration, (3) regional reseller. Decide in Phase 3 with real pilot merchants |
| Principle | Billing currency is per-merchant, fixed at subscription start; price *books* per country (not FX conversion of a THB price) |

### B. Customer display currency (what shoppers see on storefronts)

Already correct: primary currency + accepted list, display-only. The missing piece is **formatting**:

| Currency | Decimals | Format example | Note |
|---|---|---|---|
| THB | 2 (0 acceptable in casual retail) | ฿1,250.00 / 1,250 THB | Current `number_format($x, 2)` fine |
| USD | 2 | $10.50 | Fine today |
| **MMK** | **0** | **1,500 MMK / K 1,500** | **Today would render 1,500.00 MMK — wrong.** Kyat has no working decimal subdivision |
| VND (future) | 0 | 25.000 ₫ | Dot-grouped, no decimals |
| LAK / KHR (future) | 0 | 15,000 ₭ / ៛4,000 | No decimals in practice |
| JPY/KRW (already listed) | 0 | ¥1,000 | Same rule |

**Recommendation:** add a `decimals` (and later `symbol`, `symbol_position`, `thousands_sep`) map to `config/localization.php` currencies, and route all money rendering through one helper/formatter. This is the single highest-value, lowest-risk i18n change in the codebase — schedule it as the first Phase 2 item. (Not implemented in this sprint per spec.)

---

## 5. Date / Time Strategy

| Topic | Current state | Recommendation |
|---|---|---|
| **Time zones** | Per-merchant `timezone` column; curated list includes Asia/Yangon (+06:30 — PHP tz db handles the half-hour offset correctly) | Keep. Verify all merchant-facing timestamps render in *merchant* tz (audit found dashboard/orders do). Scheduled jobs (birthday, expiry) already run per-merchant — confirm tz-aware day boundaries in the Phase 2 hardening pass |
| **Locale date formats** | Carbon `translatedFormat` + per-merchant `date_format` setting | Keep. Add Burmese month names automatically via Carbon locale `my` when the locale ships |
| **Buddhist Era (TH)** | Not offered; all dates CE | **Offer BE as a merchant *display* preference for TH — Phase 1 polish.** Store everything CE/UTC forever; BE is presentation only (year + 543). Never accept BE input without explicit labelling — a 2569 typed into a CE field creates far-future data (this bug class is the reason BE stays display-only) |
| **Myanmar calendar** | — | **Do not implement.** Gregorian is standard for Myanmar business; the traditional Burmese calendar is cultural, not commercial. Not worth the complexity |
| **Week/format details** | — | TH: Monday start, d/m/Y dominant. MM: d/m/Y, Gregorian. Both already compatible with current rendering |

---

## 6. Typography (Myanmar focus)

**The Zawgyi problem is the single biggest Myanmar-specific technical risk.** Two incompatible encodings share the same codepoint range: Unicode (the standard; government-mandated since 2019) and Zawgyi (the legacy de-facto encoding still on many devices/keyboards). Text typed in one renders garbled in the other. This affects *user-generated content* — member names, product names, notes — not our translated strings.

| Area | Recommendation |
|---|---|
| **Our UI strings** | Author `lang/my/` strictly in **Unicode** (NFC-normalized). Non-negotiable |
| **Font strategy** | Self-host fonts (also a performance win — removes the bunny.net dependency): keep Figtree/system for Latin, add **Noto Sans Thai** (TH consistency; system Thai fonts vary) and **Noto Sans Myanmar** (or Padauk) subsetted, loaded only for the active locale via `unicode-range`. Budget target: ≤120KB added for MM locale |
| **Encoding risk — input** | Phase 3 options, in order: (1) accept-and-store as typed (baseline), (2) add a lightweight Zawgyi *detector* (e.g. Myanmar Tools' probabilistic model) that warns the typist, (3) optional convert-on-input to Unicode. Never silently convert without telling the user |
| **Encoding risk — display** | Render assuming Unicode. Document for support: "text looks broken" tickets in MM are usually Zawgyi input — provide a converter link in the MM help article |
| **Testing** | Add Burmese sample strings (incl. stacked consonants, medials) to the visual QA checklist for storefront, portal, poster print, and PDF/print surfaces before MM launch |
| **Line breaking** | Burmese breaks between syllables without spaces; modern browsers handle it acceptably. Avoid `word-break: break-all` on MM content; spot-check truncation ellipses on cards/tables |

---

## 7. Payment Readiness (planning only)

Two independent tracks — keep them separate in all future planning:

### Track A — OneMember billing (merchant → OneMember)

| Provider | Assessment |
|---|---|
| **Stripe** (current) | Keep as the global card rail. Fine for TH cards, SG/MY later. No Myanmar support — never the MM answer |
| **Omise (Opn)** | Thai-native PSP: **PromptPay, Thai installments, TrueMoney**, local cards with higher TH acceptance rates. Strongest candidate to *add* for Thai merchant billing — Thai SMEs strongly prefer PromptPay over cards for subscriptions. Evaluate: recurring support for PromptPay (it is push-based → likely "pay-per-cycle with reminder" model rather than auto-charge) |
| **2C2P** | Regional (SEA-wide incl. MM presence historically). Candidate for a *single* regional integration covering TH+MM+VN+KH later. Heavier onboarding; evaluate only when ≥2 non-TH markets are committed |
| **PromptPay direct (QR)** | Zero-fee manual option for TH billing: generate our own PromptPay QR per invoice + manual/semi-auto reconciliation. Low tech, high ops cost — viable bridge, not destination |
| **Myanmar (MMK)** | No international PSP covers MMK acquiring meaningfully. Realistic launch options: USD via Stripe (card-holding minority), manual bank transfer/top-up ledger, or local partner (KBZPay/WavePay business collection via reseller). Defer decision to Phase 3 pilot |

**Recommended billing sequence:** Stripe cards (today) → add Omise PromptPay for TH (Phase 1/2 boundary) → MM operational billing (Phase 3) → 2C2P evaluation when market #3 commits.

### Track B — Storefront payments (customer → merchant)

Already correctly out of scope (ADR-011): the merchant uploads their own payment QR (PromptPay today; **KBZPay/WavePay/AYA Pay QR images work identically** — zero code needed for MM). Recommendation: keep this model as long as possible; "payment confirmed" stays a merchant action. If in-flow payments are ever demanded, that is a major Type C decision (money would touch the platform) — flag it now as out of blueprint scope.

---

## 8. Legal / Compliance (areas requiring review — high level only)

| Area | Thailand | Myanmar |
|---|---|---|
| **Data protection** | **PDPA (2019)** applies now: consent records (exists — Consent/TermsAcceptance models), purpose limitation, data-subject rights (export exists; deletion path needs review), breach notification. **Priority: pre-revenue legal review** | Cybersecurity/data law evolving and politically volatile; no PDPA-equivalent maturity. Key question for counsel: cross-border hosting of Myanmar citizens' data (we host outside MM) |
| **Terms of service** | Current bundle is EN/TH **draft pending legal review (DR-33)** — must be ratified before charging Thai merchants | Needs MM-specific review + Burmese translation; enforceability of e-terms locally |
| **E-tax / invoicing** | Thai e-tax invoice requirements once billing revenue starts (VAT registration threshold, withholding tax on SaaS) | Commercial tax landscape; likely handled via partner/reseller model |
| **Consumer/loyalty rules** | Point-expiry disclosure practices; prize/sweepstake law if campaigns gamify | Same review, lower maturity; sanctions screening obligations for any payment partner |
| **Sanctions / banking** | — | **Material risk:** several Myanmar banks/entities are under international sanctions. Any billing partner, transfer route, or reseller must be screened. This alone may dictate the reseller model |
| **Employment/support ops** | — | Burmese support staffing model (contractor vs partner) has its own legal footprint |

**Action:** none now. Compile these into a legal-review brief when the PO green-lights each market; DR-33 (TH terms) is the only one already on the clock.

---

## 9. Roadmap

### Phase 1 — Thailand Hardening (pre-revenue polish; each item one small sprint)
1. Buddhist-Era display preference (presentation-only) — TH merchant delight
2. Omise/PromptPay billing evaluation + decision paper (no build) 
3. Thai Knowledge Center coverage: top-10 articles fully TH
4. PDPA + terms (DR-33) legal review executed
5. Self-hosted fonts incl. Noto Sans Thai (perf + consistency; also pre-work for MM)

### Phase 2 — International Foundations (market-agnostic machinery; no new market yet)
1. **Per-currency formatting rules** (decimals/symbol/grouping) + single money-render helper — fixes MMK/VND/LAK/KHR/JPY classes at once ← *first, highest value/lowest risk*
2. **Country address profiles** (config-driven tiers, labels, postcode required/optional/hidden, Township semantics) applied to the Commerce order form + merchant profile
3. Customer-surface string extraction audit → guarantee the customer-facing string set is small, isolated, and cheaply translatable per locale
4. Phone normalization strategy decision (E.164 storage vs display) — prerequisite for any cross-border identity work, deliberately *decided* here and *implemented* only when Identity needs it
5. Timezone-boundary hardening pass on scheduled automations (BE/expiry at +06:30 etc.)

### Phase 3 — Myanmar Activation (business-committed; sequence within phase)
1. Customer-surface Burmese translation + Noto Sans Myanmar font ship
2. Burmese Getting Started articles (6) + Burmese-speaking support arrangement (launch gate)
3. Merchant UI Burmese translation to 100% + `internal_languages` promotion + completeness tests
4. MMK activation using Phase 2 formatting rules; MM address profile on
5. Zawgyi mitigation (detector + help-article converter link)
6. MM billing decision executed (USD-Stripe / manual ledger / partner) + sanctions screening + legal review
7. Pilot cohort (5–10 Yangon merchants) before any marketing

**Explicit ordering rationale:** Phase 2 before any Myanmar work because every Phase 2 item is reused by markets 3–7; Myanmar-only work (Zawgyi, MMK billing ops, Burmese content) waits for a signed business commitment so it never becomes stranded effort.

---

## Known Risks

| # | Risk | Severity | Mitigation in this plan |
|---|---|---|---|
| R1 | Zawgyi/Unicode split corrupts MM user content | High (MM) | §6 — Unicode-only strings, detector, support playbook; accept as known limitation at pilot |
| R2 | No viable MMK billing rail | High (MM) | §7 — USD/manual/partner options; decision deferred to pilot with real merchants |
| R3 | Sanctions exposure via MM banking partners | High (MM) | §8 — screening precedes any partner; reseller model as fallback |
| R4 | MMK shown with decimals today if a merchant selects it | Medium | Phase 2 item 1 (formatting rules) scheduled first |
| R5 | Free-text addresses block future delivery features | Medium | §2 profiles designed; implemented only when a feature needs them |
| R6 | Translation debt: partial merchant locales erode trust | Medium | Policy: merchant UI locales ship only at 100% with completeness tests |
| R7 | PDPA/terms unratified while revenue starts (TH) | Medium | Phase 1 item 4 — already tracked as DR-33 |
| R8 | Webfont CDN dependency (bunny.net) on SEA mobile networks | Low | Phase 1 item 5 — self-hosting |

---

*End of blueprint. No code, schema, routes, or behaviour were changed by this sprint.*
