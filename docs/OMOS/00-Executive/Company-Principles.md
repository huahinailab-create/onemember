# Company Principles

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Core-Values.md](./Core-Values.md), [Vision.md](./Vision.md), [Mission.md](./Mission.md), [ai/00-Roles.md](../../../ai/00-Roles.md) |

---

## Purpose

Company Principles are the operating rules derived from Core Values. Where values describe what we believe, principles describe how we act.

Principles are specific enough to resolve a real disagreement. "We value simplicity" is a value. "We do not add a feature if its absence is not a reported problem" is a principle.

---

## Principles

> *(To be finalised in Sprint AI-02B. Candidate principles based on decisions already made:)*

### 1. Ship working software, not promising demos.
Every sprint delivers tested, committed code. Nothing is presented as complete that does not pass `php artisan test`. No exceptions.

### 2. Scope discipline is non-negotiable.
Every feature request goes through the sprint workflow. There is no "just also add this." Scope changes require an updated spec. Undocumented additions are scope creep, not bonuses.

### 3. The spec is the contract.
Claude Developer implements exactly what the sprint spec says. The ChatGPT CTO writes specs that are unambiguous enough to implement without follow-up questions. Ambiguity is resolved before implementation begins, not discovered during it.

### 4. Security is never a trade-off.
We do not disable security features to ship faster. We do not expose developer tools in production. We do not hardcode secrets. These are not decisions — they are invariants.

### 5. Merchant data belongs to the merchant.
We do not monetise merchant customer data. We do not share it with third parties. We do not use it for cross-merchant analytics without explicit opt-in consent.

### 6. Document decisions, not just outcomes.
Every significant architectural or product decision is recorded with its reasoning. A future reader must be able to understand why a decision was made, not just what it was.

### 7. Prefer boring technology.
We use Laravel because it is proven, well-documented, and has a large community. We use Bootstrap 5 because it is stable and consistent. We do not adopt new frameworks or libraries because they are exciting — we adopt them because they solve a specific problem we have.

### 8. The customer experience starts with the merchant experience.
If merchants are not using the platform effectively, customers never reach it. Every product decision must consider the merchant's ability to understand and operate the feature without documentation.

---

## Notes for AI-02B

When finalising this document:
- Review these candidates against actual decisions made in prior sprints
- Remove any principle that has already been violated — only record principles you will actually keep
- Add any principles that emerge from Product Bible discussions
- Aim for 8–12 principles total — enough to be meaningful, few enough to remember
