# 11 — Legal Pages

> Inventory + intent only. **Every page below ships only after legal review (DR-33 umbrella).** Until then, current draft terms remain marked draft. Legal pages are written in the same plain voice as the rest of the site — being readable *is* the trust strategy — with the legally binding text beneath plain-language summaries ("what this means" boxes).

## Required pages

| # | Page | URL | Contents (intent) | Priority |
|---|---|---|---|---|
| 1 | **Privacy Policy** | /legal/privacy | What we collect (merchant account data; members' name/phone/birthday on behalf of merchants), why, retention, sharing (none/processors list), rights, contact. Clarifies the two roles: OneMember as processor for member data, controller for merchant account data | 🔴 Before first paid merchant |
| 2 | **Terms of Service** | /legal/terms | The merchant agreement: account, acceptable use, plans/billing, data ownership (merchant owns member list — say it here too), liability, termination, governing law (TH). Versioned; acceptance recorded (mechanism already built — TermsAcceptance) | 🔴 Before first paid merchant (DR-33) |
| 3 | **Cookie Policy** | /legal/cookies | What we set (session, analytics), consent banner behavior, opt-out. Keep the actual cookie diet minimal so this page stays short | 🟠 At website launch |
| 4 | **PDPA Notice** | /legal/pdpa | Thailand-specific: lawful bases, data-subject rights (access/correct/delete/port), DPO/contact point, cross-border transfer statement, member-consent flow explanation (captured at join — already built) | 🔴 Before first paid merchant |
| 5 | **Acceptable Use Policy** | /legal/acceptable-use | No spam via member messaging, no illegal goods on storefronts, no deceptive loyalty schemes, anti-fraud; our right to suspend | 🟠 At website launch |
| 6 | **Refund Policy** | /legal/refunds | Monthly plans — cancel forward, `[refund stance per DECISION-014]`; yearly refund terms; how to request | 🔴 With paid plans |
| 7 | **Security Statement** | /legal/security | Plain-language: encryption in transit, backups, access controls, responsible-disclosure contact, incident commitment. (Marketing-adjacent — links from footer and Enterprise page) | 🟠 At website launch |

## Supporting / embedded legal surfaces (not standalone pages)

- **Member-facing consent text** at join (exists in-product) — must be reviewed together with the PDPA notice so they match. 🔴
- **Sub-processor list** (hosting, email, analytics) — appendix of Privacy, kept current.
- **Impressum-style footer line**: legal entity name, registration number, address `[per company registration]`.
- **Enterprise DPA template** — needed the first time a chain's lawyer asks (P4 persona will); prepare after the first Enterprise conversation, not before.
- **Myanmar addendum** — at MM launch gate only: local-law review of terms/privacy + Burmese translations (INTERNATIONAL-001 §8: sanctions/cross-border items).

## Process rules

1. Single source of truth: legal pages live in one place, versioned; the in-app terms link and the website page must never diverge.
2. Change protocol: material changes → version bump + merchant notice (email/in-app) with effective date; acceptance re-capture where required.
3. Language: Thai and English of equal standing `[legal to confirm which controls]`; drafts never machine-translated.
4. Every form and signup touchpoint links Privacy + Terms; cookie banner appears once, remembers, and never blocks reading the page (dark-pattern-free, per our own brand rules).
