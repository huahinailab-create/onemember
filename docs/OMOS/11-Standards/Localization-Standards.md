# Localization Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Coding-Standards.md](./Coding-Standards.md), [Brand-Standards.md](./Brand-Standards.md), [07-Architecture-Rules.md](./07-Architecture-Rules.md) |

---

## Purpose

Standards for internationalisation and localisation in OneMember — English and Thai, with structure for future markets.

---

## Standards

### Supported Languages
- `en` — English (primary, fallback)
- `th` — Thai (required for Thailand market)
- Future: `ms` (Malaysian), `vi` (Vietnamese)

### String Management
- All user-visible strings: `__('namespace.key')` or `@lang('namespace.key')`
- Never hardcode English text in Blade views
- New keys must be added to BOTH `lang/en/` and `lang/th/` simultaneously
- Thai translation can use English as a placeholder if translation is not ready — but the key must exist

### File Structure
```
lang/
├── en/
│   ├── auth.php
│   ├── navigation.php
│   ├── dashboard.php
│   ├── campaigns.php
│   ├── members.php
│   └── [feature].php
└── th/
    └── [same files]
```

### Key Naming
- Keys are snake_case
- Namespace matches the feature area
- Use descriptive names: `campaign_created_success` not `success_1`
- Parameterised strings: `__('members.points_earned', ['points' => 100, 'name' => $member->name])`

### Date and Time
- All dates displayed in merchant's configured timezone (`merchant->settings['timezone']`)
- Date format: merchant's configured format (`merchant->settings['date_format']`)
- Never hardcode `Asia/Bangkok` or `DD/MM/YYYY` — always read from settings

### Currency
- Currency symbol and format from merchant settings (`merchant->settings['currency']`)
- Thai Baht display: `฿1,234.50` or `1,234.50 THB` (defined in settings)
- Never hardcode THB or ฿ — always read from merchant settings
