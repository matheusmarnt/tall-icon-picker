# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato segue [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adota [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [Unreleased]

## [1.1.0] - 2026-03-14

### Added
- **Dual UI Adapter** — automatically detects TallStackUI v2 and falls back to native Alpine.js/Tailwind components when absent
- Native `ui/drawer` adapter: smooth slide-over with Alpine.js `x-transition`, glassmorphism panel (`backdrop-blur-xl`), and responsive widths
- Native `ui/button` adapter: indigo→violet gradient, shadow bloom on hover, `active:scale-95` press effect
- Native `ui/select` adapter: Alpine-powered multi-select dropdown with search, chips, and `$wire.set()` Livewire sync
- Native `ui/input` adapter: search input with inline magnifying-glass icon and `ring-indigo-500/50` focus ring
- `ui` config key and `TALL_ICON_PICKER_UI` env variable to force `auto` / `tallstackui` / `native`
- Mobile-first icon grid: `grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10`
- Compact mobile pagination: `‹ current/last ›` on small screens, full page buttons on `sm+`
- Indigo/violet design system: gradient accent, `ring-2 ring-indigo-500/50` selected state, gradient checkmark badge
- i18n support with `en` and `pt_BR` locales, extensible via `vendor:publish`
- `phpunit.xml` configuration file for Pest 3 / PHPUnit 11 coverage support
- Unit tests for `resolveUiAdapter()` covering all three modes

### Changed
- `tallstackui/tallstackui` moved from `require` to `suggest` — true optional dependency
- `livewire/livewire ^3.0` promoted to `require`
- Livewire view updated: all `x-ts-*` replaced with `x-tall::ui.*` adapter components
- README fully translated to English and contributing section opened to the community
- Icon grid and pagination redesigned with indigo/violet visual system

### Fixed
- `class_exists` auto-detection now meaningful (TallStackUI removed from `require`)
- Hard `use TallStackUI\...` import replaced with FQCN string to avoid PHPStan errors when package is absent
- Alpine options JSON rendered with `{!! !!}` + `JSON_HEX_*` flags to prevent `&quot;` encoding
- Typo `stefanzwi` → `stefanzwiki` in `fix-php-code-style.yml` GitHub Action

## [1.0.0] - 2026-03-12

### Added
- Componente Livewire `IconPicker` com suporte a múltiplas bibliotecas Blade Icons
- `IconDiscoveryService` para descoberta de SVGs via filesystem
- Config publicável `tall-icon-picker.php`
- Suporte a TallStackUI (`x-ts-slide`, `x-ts-button`)
- GitHub Actions: CI, code style, CHANGELOG automático

[Unreleased]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/matheusmarnt/tall-icon-picker/releases/tag/v1.0.0
