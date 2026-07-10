# 10 — SEO Strategy

> Reality check: SEO compounds from month 6+; street sales and referral win the first 100 merchants (GTM-001). We plant SEO now so that merchants 100–1,000 arrive cheaper. **Thai-language search is the battlefield** — competition is thinner than English SaaS SEO by an order of magnitude.

## Primary keywords (Thai-first)

| Cluster | Thai (primary) | English (secondary) | Target page |
|---|---|---|---|
| Category | ระบบสะสมแต้ม (points-collection system) | loyalty program for small business | Home |
| Category | ระบบสมาชิกร้านค้า (shop membership system) | customer loyalty app Thailand | Home / Features |
| Mechanic | บัตรสะสมแต้มดิจิทัล (digital stamp card) | digital stamp card | /features/campaigns |
| Retention | ทำยังไงให้ลูกค้ากลับมาซื้อซ้ำ (how to make customers repeat) | customer retention small business | Blog pillar |
| Tool | โปรแกรมสะสมแต้มฟรี (free points program) | free loyalty program | /pricing |
| Commerce | เปิดร้านออนไลน์ ไม่เสียค่าคอมมิชชั่น (sell online no commission) | commission-free online ordering | /features/commerce |

## Industry keywords (the conversion engine — one cluster per industries page)

Pattern: `ระบบสะสมแต้ม + [industry]` / `เพิ่มลูกค้าประจำ + [industry]`:
ร้านกาแฟ (coffee) · ร้านอาหาร (restaurant) · ร้านทำผม/ซาลอน (hair) · ร้านทำเล็บ (nails) · ร้านนวด/สปา (massage/spa) · โรงแรม (hotel) · ร้านค้าปลีก/มินิมาร์ท (retail) · ร้านเสื้อผ้า (fashion) · ร้านสัตว์เลี้ยง/อาบน้ำตัดขน (pets/grooming) · คลินิกความงาม (beauty clinic).
Long-tails per industry ("บัตรสะสมแต้มร้านกาแฟ ทำเอง", "ลูกค้าหาย ทำไง ร้านนวด") feed blog posts that link to the industry page.

## Thailand strategy

1. **Own Thai long-tail before Thai head terms** — 50 helpful Thai articles beat one #3 ranking for ระบบสะสมแต้ม.
2. **`/th/` as canonical market experience**: proper `hreflang` th/en pairs; Thai titles/descriptions written natively (never translated meta).
3. **Local signals:** Google Business Profile (HQ), Thai address in footer, reviews from Founding Merchants; case studies with province names ("ร้านกาแฟ เชียงใหม่ เพิ่มลูกค้าประจำ") capture geo-flavored searches.
4. **LINE/Facebook symbiosis:** Thai SME discovery often starts social → search brand later; consistent brand name spelling in Thai (วันเมมเบอร์ `[confirm transliteration]`) so brand searches resolve.

## Myanmar strategy (pre-launch, deliberately light)

1. One Burmese landing page at launch gate (not before) — Unicode, Noto Sans Myanmar (INTERNATIONAL-001 §6), targeting "member point system" Burmese phrases — thin competition, huge first-mover surface.
2. Facebook >> Google in Myanmar discovery: SEO budget minimal; the page exists mainly to legitimize partner sales conversations.
3. No machine-translated Burmese ever — brand-destroying in a Zawgyi-sensitized market.

## Content strategy & blog ideas (2/month Thai — sustainable > ambitious)

**Pillar 1 — "Regulars playbook" (retention how-tos):** ทำยังไงให้ลูกค้ากลับมา (pillar) · 7 เหตุผลที่ลูกค้าประจำหายไป · บัตรสะสมแต้มกระดาษ vs ดิจิทัล เทียบกันชัดๆ · วิธีทวงลูกค้าเก่าด้วยข้อความ LINE เดียว · ตั้งรางวัลยังไงให้ลูกค้าอยากกลับมา (ไม่ขาดทุน).
**Pillar 2 — industry guides:** เปิดร้านกาแฟยังไงให้มีลูกค้าประจำใน 90 วัน (× each segment; feeds industries pages).
**Pillar 3 — anti-commission:** ค่าคอมมิชชั่นแอปเดลิเวอรี่กินกำไรเท่าไหร่ (calculator content) · วิธีย้ายลูกค้าจากแอปมาสั่งตรง.
**Pillar 4 — merchant stories:** every pilot case study doubled as a blog post (E-E-A-T + conversion in one).

## Knowledge strategy

- Publish the merchant-manual "getting started" and industry quick-start articles publicly under /resources/help (same content already written for the in-app Knowledge Center — one source, two surfaces). Public help articles rank for "how do I…" searches *and* pre-sell capability.
- Mark up FAQ page with `FAQPage` schema; help articles with `HowTo` where honest; org/logo schema site-wide; product schema on /pricing.

## Internal linking (the engine)

- **Hub-and-spoke:** Home → Features hub & Industries hub → children; every industry page links its 2–3 relevant feature pages + 1 case study + pricing; every feature page links 2–3 industries where it shines ("see how cafés use this").
- Every blog post links: 1 industry page + 1 feature page + 1 FAQ answer (deep-linkable anchors) — never orphaned.
- Breadcrumbs sitewide; HTML sitemap in footer; XML sitemaps per locale.

## Technical baseline (details in [13 Launch Checklist](./13-Launch-Checklist.md))
Core Web Vitals green on 4G mobile · self-hosted fonts (already recommended in INTERNATIONAL-001) · clean canonical/hreflang · descriptive Thai slugs where they help, English slugs as default · og-images per page for LINE/FB share previews (Thai text on image — shares happen in LINE groups).

## Measurement
Rank tracking on the 6 primary + 10 industry clusters (Thai) monthly · organic Start-Free conversions as the only SEO KPI that matters · Search Console monthly review folded into the marketing rhythm.
