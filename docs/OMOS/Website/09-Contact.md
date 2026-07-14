# 09 — Contact Page

> Principle: **route by intent, respond in the channel Thais actually use.** One page, six doors, LINE front and center. Every path promises a response time we can keep — a kept 2-hour promise beats a broken 2-minute one.

## Page structure

**Headline:** *Talk to a human.* 
**Sub-headline:** In Thai or English — usually within 2 business hours.

### The six doors (cards with icon, promise, channel)

| Door | For | Primary channel | Promise | Routes to |
|---|---|---|---|---|
| 🛍️ **Sales** | "Is OneMember right for my shop?" | **LINE OA** (deep-link with prefilled "สนใจ OneMember") · fallback mini-form (name, shop type, province, LINE ID) | < 2 business hours | Sales CRM sheet; SALES-001 cadence B begins |
| 🧰 **Support** | Existing merchants | In-app help first (? buttons, Help Center) · LINE OA support keyword | < 2 business hours (business days) | Support queue; unanswered-by-manual questions logged as content bugs (SALES-001 §09 rule) |
| 🤝 **Partnerships** | POS vendors, agencies, resellers, Myanmar partner candidates | Form (company, country, proposal) + email alias `partners@` | < 2 business days | Founder; Myanmar leads tagged per INTERNATIONAL-001 gate |
| 📰 **Media** | Press, bloggers, events | Email alias `press@` + downloadable press kit (logo pack, product shots, boilerplate, founder photo) | < 2 business days | Founder |
| 📈 **Investors** | VCs, angels | Email alias `invest@` (no form — signal that it's founder-personal) | personal reply | Founder; investor deck (GTM-001 build item) sent selectively |
| 🌱 **Merchant Success** | Founding Merchants & key accounts | Their dedicated LINE thread (printed on their pilot welcome kit) | < 2 business hours | CS per SALES-001 §08/§09 |

### Support block (below doors)
"Fastest answers live in the app — tap the **?** on any screen." + top-5 FAQ links + Knowledge Center link. (Deflect before ticket: good for them, good for us.)

### Practical footer of page
- Business hours (ICT, Mon–Sat `[confirm]`), languages (TH/EN — MM "coming with our Myanmar launch").
- Company legal name + registered address `[per legal registration]` — trust + PDPA requirement.
- Map/photo optional later; no office worship — we're a street-visit company.

## Form & data rules
- Forms: minimum fields, LINE ID preferred over email, no CAPTCHAs harder than the signup itself; PDPA consent line under every form (links /legal/pdpa). ⚖️ pending DR-33 wording.
- Every submission auto-acknowledges (in the user's language) with the promised response time and the LINE OA link — nobody wonders if it vanished.
- All six doors log to one inbox sheet with source tags — contact volume is a KPI input (`[10-Sales-KPIs]` leads).
