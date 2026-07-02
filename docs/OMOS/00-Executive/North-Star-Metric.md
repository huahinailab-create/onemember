# North Star Metric

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Vision.md](./Vision.md), [Mission.md](./Mission.md), [09-Roadmap/Roadmap.md](../09-Roadmap/Roadmap.md), [03-Business/](../03-Business/) |

---

## Purpose

The North Star Metric (NSM) is the single number that best captures the core value OneMember delivers. It is the metric that, when it grows, signals that real value is being created for real customers.

Every product decision, sprint priority, and business investment should be optimisable against the North Star Metric.

---

## North Star Metric

> *(To be defined in Sprint AI-02B.)*

**Candidate:** Weekly Active Loyalty Members (members who earned or redeemed points in the last 7 days, across all merchants)

**Why this candidate:**
- It captures both merchant activity (they must be recording transactions) and member engagement (members must be returning)
- It grows only when merchants are successfully running their programmes
- It is immune to vanity inflation (a merchant signing up does not move it — only real transactions do)
- It scales with the platform's health: more merchants × more engaged members

---

## Supporting Metrics

*(Framework to be completed in AI-02B. Placeholder structure:)*

### Input Metrics (leading indicators)
- New merchants activated per week
- Loyalty programmes created per week
- Members added per week

### Output Metrics (lagging indicators)
- Monthly Recurring Revenue (MRR)
- Merchant retention at 90 days
- Average transactions per member per month

### Counter Metrics (guardrails)
- Member churn rate (members with no activity in 60+ days)
- Merchant churn rate (merchants with no transactions in 30+ days)
- Support ticket volume per merchant

---

## Notes for AI-02B

When finalising this document:
- Confirm the North Star candidate or propose an alternative with reasoning
- Define the exact measurement methodology (what counts as "active", what time window)
- Set baseline and target values based on current data
- Connect each supporting metric to the NSM with a clear causal hypothesis
- Identify who owns tracking and reporting for each metric
