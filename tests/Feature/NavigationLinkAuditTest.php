<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * OVERNIGHT-001 P3 — broken-link guard. Scans every Blade view for
 * `route('name'...)` calls and asserts each name is a registered route, so a
 * typo'd nav link, renamed route, or removed endpoint fails CI instead of
 * shipping a dead link into the private beta.
 */
class NavigationLinkAuditTest extends TestCase
{
    public function test_every_route_helper_in_views_resolves(): void
    {
        $defined = array_keys(Route::getRoutes()->getRoutesByName());

        $used    = [];
        $offenders = [];

        foreach ($this->bladeFiles() as $file) {
            $contents = file_get_contents($file);

            // Match route('name'  and route("name"  — the URL helper, not
            // $request->route('param') which reads a route parameter.
            preg_match_all('/(?<![>\$\w])route\(\s*[\'"]([a-zA-Z0-9._-]+)[\'"]/', $contents, $m);

            foreach ($m[1] as $name) {
                $used[$name] = true;
                if (! in_array($name, $defined, true)) {
                    $offenders[] = $name . ' in ' . str_replace(base_path() . '/', '', $file);
                }
            }
        }

        $this->assertNotEmpty($used, 'Sanity: the scan should find route() calls.');
        $this->assertSame([], array_values(array_unique($offenders)),
            "Views reference undefined routes:\n" . implode("\n", array_unique($offenders)));
    }

    /** @return list<string> */
    private function bladeFiles(): array
    {
        $dir = resource_path('views');
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $files = [];
        foreach ($rii as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
