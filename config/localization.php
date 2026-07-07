<?php

// BETA-008B — Global merchant localization: the documented, deliberately
// simple allowed-value lists (no country/currency service yet — DECISION in
// docs/08-Product-Decisions.md). Country list lives in config/countries.php.
return [

    /*
     * Currencies a merchant can price and accept in (ISO 4217 => label).
     * The merchant's `currency` column is the PRIMARY currency; additional
     * accepted currencies live in settings.accepted_currencies. Display
     * only — OneMember never converts, settles, or touches money (ADR-011).
     * Automatic currency conversion is explicitly FUTURE work.
     */
    'currencies' => [
        'THB' => 'THB – Thai Baht',
        'USD' => 'USD – US Dollar',
        'KHR' => 'KHR – Cambodian Riel',
        'LAK' => 'LAK – Lao Kip',
        'MMK' => 'MMK – Myanmar Kyat',
        'MYR' => 'MYR – Malaysian Ringgit',
        'SGD' => 'SGD – Singapore Dollar',
        'IDR' => 'IDR – Indonesian Rupiah',
        'PHP' => 'PHP – Philippine Peso',
        'VND' => 'VND – Vietnamese Dong',
        'INR' => 'INR – Indian Rupee',
        'JPY' => 'JPY – Japanese Yen',
        'KRW' => 'KRW – South Korean Won',
        'HKD' => 'HKD – Hong Kong Dollar',
        'TWD' => 'TWD – New Taiwan Dollar',
        'CNY' => 'CNY – Chinese Yuan',
        'AUD' => 'AUD – Australian Dollar',
        'NZD' => 'NZD – New Zealand Dollar',
        'EUR' => 'EUR – Euro',
        'GBP' => 'GBP – British Pound',
        'CAD' => 'CAD – Canadian Dollar',
    ],

    /*
     * Languages the merchant APP UI ships in today (lang/ directories with
     * completeness tests). The merchant's internal language
     * (settings.locale) must be one of these.
     */
    'internal_languages' => [
        'en' => 'English',
        'th' => 'ภาษาไทย (Thai)',
    ],

    /*
     * Languages a merchant may OFFER on customer-facing surfaces
     * (storefront, portal, join, order pages) — settings.customer_languages.
     * Locales without shipped translation files render via the English
     * fallback until translations exist; the list is what merchants can
     * promise customers, translation coverage is content work.
     */
    'customer_languages' => [
        'th' => 'ไทย (Thai)',
        'en' => 'English',
        'km' => 'ខ្មែរ (Khmer)',
        'lo' => 'ລາວ (Lao)',
        'my' => 'မြန်မာ (Burmese)',
        'ms' => 'Bahasa Melayu (Malay)',
        'id' => 'Bahasa Indonesia',
        'vi' => 'Tiếng Việt (Vietnamese)',
        'zh' => '中文 (Chinese)',
        'ja' => '日本語 (Japanese)',
        'ko' => '한국어 (Korean)',
        'hi' => 'हिन्दी (Hindi)',
        'fr' => 'Français (French)',
        'de' => 'Deutsch (German)',
        'es' => 'Español (Spanish)',
    ],

    /*
     * Curated timezone display list for settings/onboarding selects.
     * Validation accepts any PHP timezone identifier.
     */
    'timezones' => [
        'Asia/Bangkok'      => 'Bangkok (UTC+7)',
        'Asia/Phnom_Penh'   => 'Phnom Penh (UTC+7)',
        'Asia/Vientiane'    => 'Vientiane (UTC+7)',
        'Asia/Yangon'       => 'Yangon (UTC+6:30)',
        'Asia/Ho_Chi_Minh'  => 'Ho Chi Minh City (UTC+7)',
        'Asia/Jakarta'      => 'Jakarta (UTC+7)',
        'Asia/Singapore'    => 'Singapore (UTC+8)',
        'Asia/Kuala_Lumpur' => 'Kuala Lumpur (UTC+8)',
        'Asia/Manila'       => 'Manila (UTC+8)',
        'Asia/Hong_Kong'    => 'Hong Kong (UTC+8)',
        'Asia/Taipei'       => 'Taipei (UTC+8)',
        'Asia/Shanghai'     => 'Shanghai (UTC+8)',
        'Asia/Tokyo'        => 'Tokyo (UTC+9)',
        'Asia/Seoul'        => 'Seoul (UTC+9)',
        'Asia/Kolkata'      => 'Kolkata (UTC+5:30)',
        'Asia/Dubai'        => 'Dubai (UTC+4)',
        'Europe/London'     => 'London (UTC+0/+1)',
        'Europe/Paris'      => 'Paris (UTC+1/+2)',
        'America/New_York'  => 'New York (UTC-5/-4)',
        'America/Los_Angeles' => 'Los Angeles (UTC-8/-7)',
        'Australia/Sydney'  => 'Sydney (UTC+10/+11)',
        'Pacific/Auckland'  => 'Auckland (UTC+12/+13)',
        'UTC'               => 'UTC',
    ],

];
