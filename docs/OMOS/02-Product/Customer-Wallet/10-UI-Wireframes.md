# 10 — UI Wireframes (mobile-first, Bootstrap 5, brand: #1A2E5A / #FF1585 / #F0F0F4)

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

All screens Thai-default with 🌐 language switcher (DECISION-067 conventions). Bottom tab bar, thumb-reachable CTAs.

---

## W1 — Join Landing (from universal QR, guest)

```
┌─────────────────────────────┐
│  [merchant logo]            │
│  ปารีส คอฟฟี่               │
│  สะสมแต้มทุกการซื้อ           │
│  ─────────────────────────  │
│  ⭐ ซื้อครบ 100.- รับ 1 แต้ม  │
│  🎁 10 แต้ม = กาแฟฟรี 1 แก้ว │
│                             │
│  ┌───────────────────────┐  │
│  │ เบอร์โทรศัพท์          │  │
│  └───────────────────────┘  │
│  [ เข้าร่วมใน 15 วินาที ]   │  ← btn-primary #FF1585
│                             │
│  Powered by OneMember 🌐 EN │
└─────────────────────────────┘
```

## W2 — OTP Verify

```
┌─────────────────────────────┐
│  ← กลับ                     │
│  ยืนยันเบอร์ 081-xxx-5678     │
│                             │
│   ┌─┐ ┌─┐ ┌─┐ ┌─┐ ┌─┐ ┌─┐  │
│   │ │ │ │ │ │ │ │ │ │ │ │  │  ← 6 boxes, auto-advance
│   └─┘ └─┘ └─┘ └─┘ └─┘ └─┘  │
│                             │
│  ส่งรหัสอีกครั้ง (0:47)       │
└─────────────────────────────┘
```

## W3 — Consent Screen (join step 2)

```
┌─────────────────────────────┐
│  แบ่งปันข้อมูลกับ ปารีส คอฟฟี่    │
│                             │
│  ✅ โปรไฟล์ (จำเป็นต่อการสมัคร)│
│  ◻️ วันเกิด — รับโบนัสวันเกิด  │
│  ◻️ ข่าวสารและโปรโมชั่น        │
│  ◻️ การวิเคราะห์แบบไม่ระบุตัวตน │
│                             │
│  เปลี่ยนได้ทุกเมื่อใน "ความเป็น │
│  ส่วนตัว"  · นโยบาย PDPA     │
│                             │
│  [ ยืนยันและเข้าร่วม ]        │
└─────────────────────────────┘
```

## W4 — Wallet Home (card list)

```
┌─────────────────────────────┐
│  สวัสดี สมหญิง 👋       ⚙️   │
│  ┌───────────────────────┐  │
│  │ ▓ ปารีส คอฟฟี่        │  │ ← card bg = merchant brand colour
│  │   125 แต้ม             │  │
│  │   ▓▓▓▓▓▓▓░░░ อีก 25 → 🎁│  │
│  └───────────────────────┘  │
│  ┌───────────────────────┐  │
│  │ ▓ มิลาน สปา            │  │
│  │   แสตมป์ 7/10  ●●●●●●●○○○│  │
│  └───────────────────────┘  │
│  ＋ ค้นหาร้านใกล้คุณ          │  ← wallet_visible directory (BD-06)
│─────────────────────────────│
│  [🏠 หน้าแรก] [🔳 QR] [🔒 ความเป็นส่วนตัว] │
└─────────────────────────────┘
```

## W5 — Membership Detail

```
┌─────────────────────────────┐
│ ← ปารีส คอฟฟี่              │
│   125 แต้ม   สมาชิกตั้งแต่ 2026│
│  ┌─────────┐                │
│  │ ▓▓ QR ▓▓ │  ← OM1 rotating│
│  │ ▓▓▓▓▓▓▓ │     (60s TOTP)  │
│  └─────────┘                │
│  [  Add to Apple Wallet ]   │  ← BD-04
│  [ G  Save to Google Wallet]│
│─ รางวัลที่แลกได้ ─────────────│
│  🎁 กาแฟฟรี — 150 แต้ม (อีก 25)│
│─ ประวัติ ────────────────────│
│  +5 แต้ม  ซื้อ 500.-   5 ก.ค. │
│  -100     แลกเค้ก      1 ก.ค. │
└─────────────────────────────┘
```

## W6 — Privacy Centre

```
┌─────────────────────────────┐
│  ความเป็นส่วนตัวของฉัน         │
│                             │
│  ปารีส คอฟฟี่         [จัดการ]│
│   โปรไฟล์ ✅ วันเกิด ✅        │
│   การตลาด ❌ วิเคราะห์ ✅      │
│                             │
│  มิลาน สปา             [จัดการ]│
│   โปรไฟล์ ✅ อื่นๆ ❌          │
│─────────────────────────────│
│  📄 ดาวน์โหลดข้อมูลของฉัน      │
│  🗑  ลบบัญชี                  │
└─────────────────────────────┘
```

## W7 — Merchant App Addition (member list badge)

```
members index row:
│ สมหญิง ใจดี   081-xxx-5678   125 แต้ม  [👛 in wallet] │

merchant settings → wallet:
│ ◉ แสดงร้านใน Wallet directory (BD-06)                │
│ [ ⬇ ดาวน์โหลดโปสเตอร์ QR (A5 PDF) ]                  │
```

## Component Notes

- All cards are standard Bootstrap `card` with CSS variables for merchant brand colour; no inline styles (SEC-002 rule for new views).
- QR screen brightness hint + tap-to-enlarge for scanning at counters.
- Empty state (0 memberships): single big "scan a QR at any OneMember shop" illustration + camera hint.
- Offline (PWA): last-known balances shown with "as of" timestamp; QR still renders (member_code static part) with warning that fresh scan needed if older than 24 h.
