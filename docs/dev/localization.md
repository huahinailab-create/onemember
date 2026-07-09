# Localization Architecture (P10, builds on BETA-008B / DECISION-095)

Two independent language axes per merchant:
- **Internal language** (`settings.locale`) — merchant dashboard UI. Allowed
  values: `config/localization.php` `internal_languages` (en, th today).
  Config-driven end to end (SetLocale middleware, LocaleController, settings).
- **Customer-facing languages** (`settings.customer_languages`) — ordered,
  first = default; offered on storefront/portal/join/order pages; resolution:
  `?lang=` if offered → visitor session choice if offered → merchant default.
  Never browser-derived (GLOBAL-001 §8).

Placeholder locales shipped: km, my, lo, vi, zh, ja, ko (`lang/<locale>/`
with metadata only). Missing keys fall back to English (`fallback_locale`).

## Promoting a placeholder to a full internal language
1. Copy `lang/en/*.php` to `lang/<locale>/` and translate.
2. Add the locale to `internal_languages` in `config/localization.php`.
3. Extend `TranslationCompletenessTest` to enforce parity for it.
4. Currencies/timezones already cover the region (BETA-008B lists).
