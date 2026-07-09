<?php

// PLATFORM-002 P10 — placeholder locale (Korean / 한국어).
// Customer-facing surfaces may already offer this language (BETA-008B);
// strings fall back to English until this directory is translated.
// To promote to a full language: copy lang/en/*, translate, then add
// the locale to config/localization.php internal_languages and extend
// TranslationCompletenessTest. See docs/dev/localization.md.
return [
    '_locale_name'   => 'Korean',
    '_locale_native' => '한국어',
    '_direction'     => 'ltr',
];
