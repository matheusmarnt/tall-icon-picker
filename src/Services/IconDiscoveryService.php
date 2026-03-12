<?php

namespace Matheusmarnt\TallIconPicker\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class IconDiscoveryService
{
    /**
     * Returns the configured libraries and the icon count for each one.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function getAvailableLibraries(): array
    {
        $libraries = Config::get('tall-icon-picker.libraries', []);

        return collect($libraries)
            ->map(function (array $lib, string $prefix): array {
                $dir = base_path("vendor/{$lib['package']}/{$lib['path']}");
                $count = is_dir($dir) ? count(glob("{$dir}/*.svg") ?: []) : 0;

                return [
                    'id' => $prefix,
                    'name' => "{$lib['label']} ({$count})"
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Discover and browse icons by filtering through your search and selected libraries.
     */
    public function discoverIcons(array $selectedLibraries, string $search, int $page, int $perPage): LengthAwarePaginator
    {
        $allIcons = collect();
        $configuredLibraries = Config::get('tall-icon-picker.libraries', []);
        $needle = strtolower(trim($search));

        foreach ($selectedLibraries as $prefix) {
            if (!isset($configuredLibraries[$prefix])) {
                continue;
            }

            $lib = $configuredLibraries[$prefix];
            $baseDir = base_path("vendor/{$lib['package']}/{$lib['path']}");

            if (!is_dir($baseDir)) {
                continue;
            }

            $files = glob("{$baseDir}/*.svg") ?: [];

            foreach ($files as $file) {
                $slug = basename($file, '.svg');

                if ($needle !== '' && !str_contains($slug, $needle)) {
                    continue;
                }

                $allIcons->push("{$prefix}-{$slug}");
            }
        }

        $allIcons = $allIcons->sort()->values();
        $items = $allIcons->forPage($page, $perPage);

        return new LengthAwarePaginator($items, $allIcons->count(), $perPage, $page);
    }
}