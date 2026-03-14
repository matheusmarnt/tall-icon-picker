<?php

declare(strict_types=1);

namespace Matheusmarnt\TallIconPicker\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class IconDiscoveryService
{
    protected string $vendorPath;

    public function __construct(?string $vendorPath = null)
    {
        $this->vendorPath = $vendorPath ?? base_path('vendor');
    }

    /**
     * Retorna as bibliotecas configuradas e o total de ícones de cada uma.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function getAvailableLibraries(): array
    {
        /** @var array<string, array{package: string, path: string, label: string}> $libraries */
        $libraries = Config::get('tall-icon-picker.libraries', []);

        return collect($libraries)
            ->map(function (array $lib, string $prefix): array {
                $dir = "{$this->vendorPath}/{$lib['package']}/{$lib['path']}";
                $count = is_dir($dir) ? count(glob("{$dir}/*.svg") ?: []) : 0;

                return [
                    'id' => $prefix,
                    'name' => "{$lib['label']} ({$count})",
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Descobre e pagina ícones filtrando por bibliotecas selecionadas e busca.
     *
     * @param  array<string>  $selectedLibraries
     * @return LengthAwarePaginator<int, string>
     */
    public function discoverIcons(
        array $selectedLibraries,
        string $search,
        int $page,
        int $perPage
    ): LengthAwarePaginator {
        /** @var Collection<int, string> $allIcons */
        $allIcons = collect();
        /** @var array<string, array{package: string, path: string, label: string}> $configuredLibraries */
        $configuredLibraries = Config::get('tall-icon-picker.libraries', []);
        $needle = strtolower(trim($search));

        foreach ($selectedLibraries as $prefix) {
            if (! isset($configuredLibraries[$prefix])) {
                continue;
            }

            $lib = $configuredLibraries[$prefix];
            $baseDir = "{$this->vendorPath}/{$lib['package']}/{$lib['path']}";

            if (! is_dir($baseDir)) {
                continue;
            }

            foreach (glob("{$baseDir}/*.svg") ?: [] as $file) {
                $slug = basename($file, '.svg');

                if ($needle !== '' && ! str_contains($slug, $needle)) {
                    continue;
                }

                $allIcons->push("{$prefix}-{$slug}");
            }
        }

        $allIcons = $allIcons->sort()->values();
        /** @var Collection<int, string> $items */
        $items = $allIcons->forPage($page, $perPage);

        return new LengthAwarePaginator($items, $allIcons->count(), $perPage, $page);
    }
}
