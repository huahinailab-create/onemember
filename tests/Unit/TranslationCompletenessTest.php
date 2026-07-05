<?php

namespace Tests\Unit;

use Tests\TestCase;

class TranslationCompletenessTest extends TestCase
{
    /**
     * Every English translation key must have a Thai counterpart and vice
     * versa. Missing Thai keys silently fall back to English in production
     * (Engineering Backlog TEST-006).
     */
    public function test_every_english_key_has_a_thai_key(): void
    {
        $missing = $this->diffLocales('en', 'th');
        $this->assertSame([], $missing, 'Keys present in lang/en but missing in lang/th: ' . implode(', ', $missing));
    }

    public function test_every_thai_key_has_an_english_key(): void
    {
        $missing = $this->diffLocales('th', 'en');
        $this->assertSame([], $missing, 'Keys present in lang/th but missing in lang/en: ' . implode(', ', $missing));
    }

    /** @return list<string> keys in $from missing from $to, as "file.dot.key" */
    private function diffLocales(string $from, string $to): array
    {
        $missing = [];

        foreach (glob(lang_path("{$from}/*.php")) as $fromFile) {
            $name   = basename($fromFile, '.php');
            $toFile = lang_path("{$to}/{$name}.php");

            $fromKeys = $this->flattenKeys(require $fromFile);
            $toKeys   = file_exists($toFile) ? $this->flattenKeys(require $toFile) : [];

            foreach (array_diff($fromKeys, $toKeys) as $key) {
                $missing[] = "{$name}.{$key}";
            }
        }

        return $missing;
    }

    /** @return list<string> dot-notation keys of a nested translation array */
    private function flattenKeys(array $translations, string $prefix = ''): array
    {
        $keys = [];

        foreach ($translations as $key => $value) {
            $full = $prefix === '' ? (string) $key : "{$prefix}.{$key}";
            if (is_array($value)) {
                $keys = array_merge($keys, $this->flattenKeys($value, $full));
            } else {
                $keys[] = $full;
            }
        }

        return $keys;
    }
}
