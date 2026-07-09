<?php

namespace App\Marketplace;

use Illuminate\Support\Collection;

/**
 * PLATFORM-002 Part 1 — App Registry.
 *
 * Single source of truth for which Apps exist on the platform. Reads
 * config/apps.php and exposes typed Manifests. Third-party or dynamically
 * discovered apps register at boot via register() — Core never needs to
 * change to learn about a new app.
 */
class AppRegistry
{
    /** @var array<string, Manifest> */
    private array $manifests = [];

    public function __construct()
    {
        foreach (config('apps.registry', []) as $key => $entry) {
            $this->manifests[$key] = Manifest::fromConfig($key, $entry);
        }
    }

    /** Register an additional manifest at runtime (future third-party path). */
    public function register(Manifest $manifest): void
    {
        $this->manifests[$manifest->key] = $manifest;
    }

    /** @return Collection<string, Manifest> */
    public function all(): Collection
    {
        return collect($this->manifests);
    }

    public function has(string $key): bool
    {
        return isset($this->manifests[$key]);
    }

    public function get(string $key): ?Manifest
    {
        return $this->manifests[$key] ?? null;
    }

    /** @return Collection<string, Manifest> apps installable today */
    public function available(): Collection
    {
        return $this->all()->filter(fn (Manifest $m) => $m->isAvailable());
    }

    /** @return list<string> app keys that declare $key as a dependency */
    public function dependentsOf(string $key): array
    {
        return $this->all()
            ->filter(fn (Manifest $m) => in_array($key, $m->dependencies, true))
            ->keys()
            ->all();
    }

    /**
     * Registry-level health snapshot: every available app must have a valid
     * manifest, resolvable provider class, and existing migrations path.
     * Per-merchant install state is AppManager::healthFor().
     */
    public function health(): array
    {
        return $this->all()->map(function (Manifest $m) {
            $problems = [];

            if ($m->provider && ! class_exists($m->provider)) {
                $problems[] = "provider {$m->provider} missing";
            }
            if ($m->migrationsPath && ! is_dir(base_path($m->migrationsPath))) {
                $problems[] = "migrations path {$m->migrationsPath} missing";
            }
            foreach ($m->dependencies as $dep) {
                if (! $this->has($dep)) {
                    $problems[] = "unknown dependency {$dep}";
                }
            }

            return [
                'key'     => $m->key,
                'version' => $m->version,
                'status'  => $m->status,
                'healthy' => $problems === [],
                'problems' => $problems,
            ];
        })->values()->all();
    }
}
