<?php

// PLATFORM-002 P10 — placeholder locale (Chinese / 中文).
// Customer-facing surfaces may already offer this language (BETA-008B);
// strings fall back to English until this directory is translated.
// To promote to a full language: copy lang/en/*, translate, then add
// the locale to config/localization.php internal_languages and extend
// TranslationCompletenessTest. See docs/dev/localization.md.
return [
    '_locale_name'   => 'Chinese',
    '_locale_native' => '中文',
    '_direction'     => 'ltr',
];
