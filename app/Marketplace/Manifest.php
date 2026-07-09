<?php

namespace App\Marketplace;

/**
 * PLATFORM-002 Part 1 — App Manifest.
 *
 * Immutable metadata describing one OneMember App (ADR-012 Layer 2).
 * Built from a config/apps.php registry entry; every field beyond `key`
 * is optional so legacy two-field entries (icon/status) keep working.
 *
 * Apps remain first-party modules inside the Laravel monolith. A future
 * third-party app supplies the same manifest shape (plus a provider class)
 * without any change to Core code.
 */
class Manifest
{
    public function __construct(
        public readonly string $key,
        public readonly string $icon,
        public readonly string $status,          // available | coming_soon | deprecated
        public readonly string $version,
        public readonly string $category,
        /** @var list<string> other app keys required before install */
        public readonly array $dependencies,
        /** @var list<string> ability strings the app introduces (e.g. queue.tickets.manage) */
        public readonly array $permissions,
        /** @var array<string,bool> feature flags the app ships with (flag => default) */
        public readonly array $featureFlags,
        /** @var array<string,mixed> default per-merchant configuration */
        public readonly array $defaultConfig,
        /** @var list<array{route:string,icon:string,label:string}> sidebar items */
        public readonly array $navigation,
        public readonly ?string $provider,       // SDK AppProvider subclass, when the app has one
        public readonly ?string $migrationsPath, // relative to base_path()
        public readonly ?string $seeder,         // FQCN of an optional seeder
        public readonly ?string $docs,           // repo-relative documentation path
    ) {
    }

    public static function fromConfig(string $key, array $entry): self
    {
        return new self(
            key: $key,
            icon: $entry['icon'] ?? 'bi-grid',
            status: $entry['status'] ?? 'coming_soon',
            version: $entry['version'] ?? '1.0.0',
            category: $entry['category'] ?? 'general',
            dependencies: $entry['dependencies'] ?? [],
            permissions: $entry['permissions'] ?? [],
            featureFlags: $entry['feature_flags'] ?? [],
            defaultConfig: $entry['default_config'] ?? [],
            navigation: $entry['navigation'] ?? [],
            provider: $entry['provider'] ?? null,
            migrationsPath: $entry['migrations'] ?? null,
            seeder: $entry['seeder'] ?? null,
            docs: $entry['docs'] ?? null,
        );
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /** Localized display name (lang key pattern established in CORE-002). */
    public function name(): string
    {
        return __('apps.name_' . $this->key);
    }

    public function toArray(): array
    {
        return [
            'key'           => $this->key,
            'icon'          => $this->icon,
            'status'        => $this->status,
            'version'       => $this->version,
            'category'      => $this->category,
            'dependencies'  => $this->dependencies,
            'permissions'   => $this->permissions,
            'feature_flags' => $this->featureFlags,
            'navigation'    => $this->navigation,
            'docs'          => $this->docs,
        ];
    }
}
