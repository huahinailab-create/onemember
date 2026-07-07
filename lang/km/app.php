<?php

// PLATFORM-002 P10 — placeholder locale (Khmer / ខ្មែរ).
// Customer-facing surfaces may already offer this language (BETA-008B);
// strings fall back to English until this directory is translated.
// To promote to a full language: copy lang/en/*, translate, then add
// the locale to config/localization.php internal_languages and extend
// TranslationCompletenessTest. See docs/dev/localization.md.
return [
    '_locale_name'   => 'Khmer',
    '_locale_native' => 'ខ្មែរ',
    '_direction'     => 'ltr',
];
