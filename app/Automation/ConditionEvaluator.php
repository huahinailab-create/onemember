<?php

namespace App\Automation;

/**
 * PLATFORM-002 Part 6 — evaluates rule conditions against an event payload.
 *
 * Conditions are ANDed: every row must match. Supported operators:
 * equals, not_equals, gt, gte, lt, lte, contains, exists.
 * Unknown fields fail closed (condition does not match), so a mis-typed
 * rule can never fire on unintended data.
 */
class ConditionEvaluator
{
    /** @param list<array{field:string,operator:string,value?:mixed}> $conditions */
    public function matches(array $conditions, array $payload): bool
    {
        foreach ($conditions as $condition) {
            if (! $this->matchesOne($condition, $payload)) {
                return false;
            }
        }

        return true;
    }

    private function matchesOne(array $condition, array $payload): bool
    {
        $field    = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $expected = $condition['value'] ?? null;

        if ($field === null) {
            return false;
        }

        $exists = array_key_exists($field, $payload);
        $actual = $payload[$field] ?? null;

        return match ($operator) {
            'exists'     => $exists,
            'equals'     => $exists && $actual == $expected,
            'not_equals' => $exists && $actual != $expected,
            'gt'         => $exists && is_numeric($actual) && $actual > $expected,
            'gte'        => $exists && is_numeric($actual) && $actual >= $expected,
            'lt'         => $exists && is_numeric($actual) && $actual < $expected,
            'lte'        => $exists && is_numeric($actual) && $actual <= $expected,
            'contains'   => $exists && is_string($actual) && str_contains(mb_strtolower($actual), mb_strtolower((string) $expected)),
            default      => false,
        };
    }
}
