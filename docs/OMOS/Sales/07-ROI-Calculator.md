# 07 — ROI Calculator (formulas + worked examples)

> **Formulas only — this documents the math**; the interactive tool (web/sheet) is a build item in GO-TO-MARKET-001 §11. Until it exists, do this on paper at the counter — hand-written math with *her* numbers is more persuasive than any app.
> Rules: use the merchant's own numbers from [Discovery](./03-Discovery-Questions.md) (never invent), round conservatively DOWN, present ranges not promises, exclude the reward cost honestly.

## Inputs (from discovery)

| Symbol | Meaning | Typical source |
|---|---|---|
| `C` | Customers served per day | Q: "how many a day?" |
| `T` | Average ticket (฿) | Q: "typical spend?" |
| `D` | Operating days/month | usually 26–30 |
| `R` | Share of customers who are repeat-capable | conservative default 40% |
| `V` | Current visits/month per regular | segment default (café 6, salon 1, resto 2, nails 1.5) |
| `U` | Uplift in visit frequency from loyalty | **conservative 5–10%** (industry lore says 15–25%+; never claim it) |
| `M` | Gross margin on the incremental sale | segment default (café 65%, resto 55%, salon 75%, retail 30%) |
| `G` | Reward giveaway cost as % of incremental revenue | default 10% |
| `P` | Monthly plan price (฿) | per DECISION-014 grid |

## Core formulas

```
Regulars                 N  = C × D × R ÷ V        (unique repeat customers/month)
Enrolled members (mo.3)  E  = N × 60%              (realistic enrolment share)
Extra visits/month       ΔVis = E × V × U
Extra revenue/month      ΔRev = ΔVis × T
Extra gross profit       ΔGP  = ΔRev × M × (1 − G)
Net monthly gain         Net  = ΔGP − P
ROI multiple             ROI  = ΔGP ÷ P
Payback period (days)    PB   = 30 × P ÷ ΔGP
Break-even extra visits  BE   = P ÷ (T × M × (1 − G))   ← the killer number
```

**Retention framing (secondary):** if churn of regulars drops from ~3%/mo to ~2%/mo, a regular's lifetime extends ~50% → customer lifetime value `LTV = T × V × M × (1 ÷ monthly churn)` — use only with P3/P4 personas who think in LTV; street merchants think in visits.

## Worked example 1 — Coffee shop (the standard napkin)

Inputs: C=150/day, T=฿120, D=30, R=40%, V=6, U=7%, M=65%, G=10%, P=฿(Starter, illustrative ฿390*)

```
N    = 150×30×0.40 ÷ 6            = 300 regulars
E    = 300 × 60%                  = 180 members by month 3
ΔVis = 180 × 6 × 0.07             ≈ 75 extra visits/month
ΔRev = 75 × 120                   = ฿9,000/month
ΔGP  = 9,000 × 0.65 × 0.90        ≈ ฿5,265/month
ROI  = 5,265 ÷ 390                ≈ 13×
PB   ≈ 30×390 ÷ 5,265             ≈ 2.2 days
BE   = 390 ÷ (120×0.65×0.90)      ≈ 6 extra visits/month
```
**The sentence that sells:** *"If this brings back six extra coffees a month — six, total, from all your regulars — it's paid for itself. Everything after that is profit."*

## Worked example 2 — Hair salon

Inputs: C=12/day, T=฿450, D=26, R=70%, V=1 (monthly cycle), U=8%, M=75%, G=10%, P=฿390*

```
N    = 12×26×0.70 ÷ 1             ≈ 218 regulars
E    = 218 × 60%                  ≈ 130 members
ΔVis = 130 × 1 × 0.08             ≈ 10 extra visits/month
ΔRev = 10 × 450                   = ฿4,500/month
ΔGP  = 4,500 × 0.75 × 0.90        ≈ ฿3,038/month
ROI  ≈ 7.8×      PB ≈ 3.9 days    BE ≈ 1.3 visits/month
```
**Sentence:** *"One client per month who would have drifted away — that's the whole cost. Your win-back list will have thirty."*

## Worked example 3 — Restaurant (with storefront angle)

Inputs: C=80 covers/day, T=฿250, D=30, R=35%, V=2, U=6%, M=55%, G=10%, P=฿(Professional, illustrative ฿790*)

```
N    = 80×30×0.35 ÷ 2             = 420 regulars
E    = 420 × 60%                  = 252 members
ΔVis = 252 × 2 × 0.06             ≈ 30 extra tables/month
ΔRev = 30 × 250                   = ฿7,500/month
ΔGP  = 7,500 × 0.55 × 0.90        ≈ ฿3,713/month
ROI  ≈ 4.7×      PB ≈ 6.4 days    BE ≈ 6.4 tables/month
```
**Storefront bonus math:** every direct order replacing a 30%-commission delivery order keeps `T × 30%` = ฿75. Just 10 switched orders/month ≈ ฿750 — roughly another plan payment. *"The loyalty pays for it; the commission savings are free money."*

## Presentation rules

1. Write it by hand in front of her, narrating each line with *her* numbers.
2. Always end at **BE (break-even visits)** — smallest, most believable number.
3. Say the caveat out loud: "estimates, conservative on purpose — your dashboard will show the real numbers."
4. Never compare against her doing nothing; compare against what she already pays (delivery commissions, FB boosts).

\* Illustrative placeholder prices only — final amounts are **DECISION-014** (PO). Update all examples the day pricing is ratified.
