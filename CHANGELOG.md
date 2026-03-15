# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.5.0] - 2026-03-15

### Changed
- **Restored v1.3.0 Alpine/Livewire logic across all native components** — business logic and interaction patterns downgraded to the proven v1.3.0 baseline while keeping the current visual interface intact:
  - **`ui/drawer`** — backdrop `@click="open = false"` (explicit click-to-close on the overlay, matching v1.3.0) replaces `@click.outside` on the panel; backdrop now fades in/out with dedicated `x-transition:enter/leave` directives instead of the shorthand `x-transition.opacity`
  - **`ui/select`** — `get selected()` reads `$wire.property` directly (reactive via Livewire's `$wire` Proxy) and writes use `$wire.set()` explicitly, restoring the exact binding pattern from v1.3.0 and eliminating `$wire.$entangle()` which caused initialization-order failures with stale published views; options JSON is stored in a `data-options` attribute rendered via `{{ }}` (htmlspecialchars), keeping `x-data` free of Blade-interpolated JSON content and safe in any HTML attribute quote style
  - **`src/Livewire/IconPicker.php`** — `resetFilters()` retained (present in v1.3.0 and current)
  - **`ui/button`**, **`ui/input`**, **`icon-picker.blade.php`** — no logic changes; all `wire:*` and Alpine directives already matched v1.3.0

## [1.4.3] - 2026-03-15

### Fixed
- **CRITICAL — `x-data` attribute still broken in consumers with published views** — even after v1.4.1 switched to single-quoted `x-data`, consumers who had previously published views retained the old double-quoted `x-data` with `{!! Js::from() !!}` inline, causing Alpine `Unexpected token '}'` and cascading `search/filtered/triggerText is not defined` errors. Root fix: the options JSON and locale strings are moved out of `x-data` entirely into `data-options`, `data-selected-text`, and `data-placeholder-text` HTML attributes rendered via `{{ }}` (htmlspecialchars); the `x-data` string now contains zero Blade-interpolated JSON or locale content and is safe regardless of HTML attribute quote style. Additionally, `$wire.$entangle()` is replaced with the proven v1.3.0 pattern — `get selected() { return $wire.property }` as a reactive getter and `$wire.set()` as an explicit setter — eliminating any risk of entangle cleanup or initialization-order issues.

## [1.4.2] - 2026-03-15

### Changed
- **Pre-computed JS variables in native `ui/select`** — moved `Js::from()` calls out of the `x-data` template string into the `@php` block; no behaviour change

## [1.4.1] - 2026-03-15

### Fixed
- **CRITICAL — truncated `x-data` in native `ui/select` (pt_BR and any locale containing quotes)** — `@js()` emits values delimited by literal double quotes; inside `x-data="..."` (also delimited by `"`), the HTML5 parser terminated the attribute at the first inner `"`, corrupting the DOM and silently breaking the initialisation of **all** Alpine/Livewire components on the page with no console errors. Fixed by switching the attribute delimiter to single quotes (`x-data='...'`) and replacing `@js()` with `{{ Js::from(...) }}`, whose output uses `JSON_HEX_QUOT` and is safe in any HTML attribute context
- **Memory leak in `@entangle` in native `ui/select` (Livewire 4)** — `@entangle($wireProperty)` compiled to `window.Livewire.find(id).entangle(name)`, which in Livewire 4 received `cleanup2 = undefined` and never released the listener when the component was destroyed. Replaced with `$wire.$entangle({{ Js::from($wireProperty) }})`, which routes through the correct Alpine magic path with automatic cleanup
- **Boot order in `TallIconPickerServiceProvider`** — `Config::set('tall-icon-picker.ui', ...)` was called immediately in `boot()` while Livewire component registration was deferred to `booted()`. Moved `Config::set` inside the `booted()` callback, before the registrations, ensuring adapter resolution and component registration share the same context

## [1.4.0] - 2026-03-15

### Added
- **New i18n keys for native adapter** — `libraries_hint`, `search_label`, `search_hint`, `clear_filters`, `previous_page`, `next_page`, `selected`, and `select_placeholder` added to both `en` and `pt_BR` locale files, completing i18n coverage for every user-facing string in the native UI path

### Changed
- **Native view fully i18n-aware** — `icon-picker.blade.php` now resolves every label, hint, placeholder, aria-label, and button text through `__('tall-icon-picker::icon-picker.*')` with no hardcoded English strings remaining
- **Native UI adapter components i18n-aware** — `ui/select.blade.php` reads `selected` and `select_placeholder` translations via `@js(__(...))` so Alpine receives the correct locale text at render time; `ui/input.blade.php` and `ui/button.blade.php` delegate labels/hints to the caller view, keeping adapter components locale-agnostic

### Fixed
- **`svg()` exception in icon grid** — `{!! svg($icon) !!}` in the icon grid is now wrapped in a `@php try/catch (\Throwable) @endphp` block, consistent with the existing guard in `selectedIconSvg`; prevents fatal view exceptions when an icon slug cannot be resolved (e.g. in test environments with mocked paginator results)

## [1.3.0] - 2026-03-15

### Added
- **Dedicated native view** — the native adapter now renders a fully self-contained Blade view (`icon-picker.blade.php`) that never touches TallStackUI components; the TallStackUI path routes to its own preserved view (`icon-picker-tallstackui.blade.php`); `render()` in `IconPicker` picks the correct view based on `config('tall-icon-picker.ui')`
- **Inline library multi-select** — the native view embeds its own Alpine-powered multi-select (light-mode-first: `bg-white border-gray-200`) directly in the filter panel; no longer delegates to the `ui/select` adapter component in the native path
- **Filter panel label row** — small `text-xs` labels ("Libraries", "Search") appear above each filter inside the panel for improved scannability

### Changed
- **`aspect-square` icon tiles** — icon grid buttons now enforce equal width and height via `aspect-square`; the previous fixed-height approach caused inconsistent tile sizes on some viewport widths
- **Hover lift effect on icon tiles** — `hover:-translate-y-1 hover:shadow-md` replaces `hover:scale-105`; the vertical lift communicates interactivity more clearly at high icon density without visual crowding
- **Filters layout** — search and library selector are grouped in a unified `bg-gray-50 dark:bg-zinc-800/60 rounded-xl` panel; on `md:` and above they sit side-by-side in a 2-column grid
- **Stats bar** — now rendered as a `bg-gray-50 dark:bg-zinc-800/40 rounded-lg` pill row; active search term is highlighted in `text-indigo-600` next to a `·` separator
- **Scoped loading states** — skeleton and grid use `wire:target="search, libraries, page"` instead of bare `wire:loading`, so unrelated Livewire requests no longer trigger the skeleton
- **Pagination arrows** — replaced HTML entity `‹ ›` with explicit `<svg>` chevron paths (`M15 19l-7-7 7-7` / `M9 5l7 7-7 7`) for pixel-perfect rendering across all browsers and font stacks
- **Empty state** — surrounded by a `border-dashed border-gray-200 rounded-xl` container to reinforce the empty region boundary
- **Choose button** — redesigned with indigo accent (`bg-indigo-50 border-indigo-200 text-indigo-700`) and `active:scale-95` micro-animation; visually distinct from neutral secondary buttons
- **Cancel button in drawer footer** — now rendered as an inline `<button>` with full light/dark styling instead of the `x-tall::ui.button` adapter component, ensuring consistent appearance in the native path
- **Skeleton count** — increased from 50 to 60 placeholder tiles to match the new default `perPage` value and fill the grid uniformly

## [1.2.1] - 2026-03-14

### Fixed
- `TypeError: Cannot assign null to property IconPicker::$placeholder of type string` — the Blade wrapper defaults `placeholder` to `null`; passing it directly to the Livewire component violated PHP 8 strict typing. Fixed with `$placeholder ?? ''` in the wrapper.

## [1.2.0] - 2026-03-14

### Added
- **Validation error display** — the Blade wrapper reads `$errors->first($resolvedModel)` using the `wire:model` field name and renders the error message below the field with `role="alert"` for screen-reader accessibility; label transitions to red when an error is active
- **`hint` prop** — helper text rendered below the field with an info icon; hidden when a validation error is active (error takes priority)
- **`placeholder` prop** — customises the empty-state text inside the trigger field; falls back to the `no_icon_selected` translation key when omitted; fixes missing default that would throw if the prop was absent
- **`placeholder` on `IconPicker` component** — added `public string $placeholder = ''` to the Livewire component so the value flows from the Blade wrapper into the trigger view

### Changed
- **Mobile-first icon grid** — `grid-cols-5` on mobile (previously `grid-cols-4`), keeping `sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10`; 25% more icons visible on small screens without scrolling
- **Trigger field redesigned** — full-width clickable button with subtle indigo hover state and a search icon indicator; clear button extracted as a standalone icon button with red hover feedback
- **Icon tile hover** — `hover:scale-105` (softer than `hover:scale-110` at high density) + `group-hover:text-indigo-600/400` colour shift on the icon
- **Pagination touch targets** — buttons enlarged to `h-9 w-9` (36 px) for better mobile usability
- **Native drawer** — `@keydown.escape.window` closes the panel; `aria-label` added to the close button; panel is full-width on mobile, `sm:max-w-xl` on tablet
- **Native button secondary** — explicit `bg-white dark:bg-zinc-800` with visible border for light/dark parity
- **Native input** — corrected to light-mode-aware colours (`bg-white border-gray-200 text-gray-900`); previously used dark-only zinc palette

### Fixed
- **Parent property not updated on icon select / clear** — the old `$wire.$parent.$set()` Alpine call was removed in Livewire v4. Restored a reliable sync mechanism: the component now dispatches `icon-picked` from both `selectIcon()` and `clearIcon()`, and an Alpine `x-on:icon-picked.window` listener uses `Livewire.find()` + DOM traversal to call `.set(property, value)` on the parent Livewire component. A `$parentModel` filter prevents cross-picker interference when multiple pickers share the same page.

## [1.1.3] - 2026-03-14

### Fixed
- `Alpine Expression Error: Unexpected token ';'` and `selected/filtered/search is not defined` in the native `ui/select` component: the options JSON was embedded raw inside a double-quoted `x-data` attribute, causing the HTML parser to close the attribute at the first `"` in the JSON. Fixed by moving the JSON to a `data-options` attribute (rendered via `{{ }}` so `"` is HTML-escaped to `&quot;`) and reading it via `JSON.parse($el.dataset.options)` inside `x-data`

## [1.1.2] - 2026-03-14

### Fixed
- `ComponentNotFoundException: Unable to find component [tall::icon-picker]` on Livewire v4: Livewire v4 dropped `::` as a namespace separator in component tags; the component is now registered under both `tall.icon-picker` (v4 dot notation) and `tall::icon-picker` (v3 backward compat)
- Blade wrapper updated to use `<livewire:tall.icon-picker>` — compatible with both Livewire v3 and v4

## [1.1.1] - 2026-03-14

### Fixed
- Defer `Livewire::component()` registration to `app()->booted()` callback to fix `ComponentNotFoundException` on Livewire v4, where the component registry is not yet fully initialised during `ServiceProvider::boot()`

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
- `livewire/livewire` constraint broadened to `^3.0|^4.0` — full Livewire v4 support
- Livewire view updated: all `x-ts-*` replaced with `x-tall::ui.*` adapter components
- README fully translated to English and contributing section opened to the community
- Icon grid and pagination redesigned with indigo/violet visual system

### Fixed
- `class_exists` auto-detection now meaningful (TallStackUI removed from `require`)
- Hard `use TallStackUI\...` import replaced with FQCN string to avoid PHPStan errors when package is absent
- Alpine options JSON rendered with `{!! !!}` + `JSON_HEX_*` flags to prevent `&quot;` encoding
- Removed deprecated `$wire.$parent.$set()` Alpine listener (incompatible with Livewire v4); sync handled by `#[Modelable]`
- Replaced non-existent `stefanzwiki/git-auto-commit-action` with native git commands in CI
- Removed invalid `version`, `repository` and `bugs` fields from `composer.json`
- Added `phpunit.xml` to fix `--coverage` flag resolving `--cache-directory` as XML path in PHPUnit 11

## [1.0.0] - 2026-03-12

### Added
- `IconPicker` Livewire component with support for multiple Blade Icons libraries
- `IconDiscoveryService` for SVG discovery via filesystem
- Publishable `tall-icon-picker.php` config file
- TallStackUI support (`x-ts-slide`, `x-ts-button`)
- GitHub Actions: CI, code style, automatic CHANGELOG

[Unreleased]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.5.0...HEAD
[1.5.0]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.4.3...v1.5.0
[1.4.3]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.4.2...v1.4.3
[1.4.2]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.3.0...v1.4.0
[1.3.0]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.2.1...v1.3.0
[1.2.1]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.1.3...v1.2.0
[1.1.3]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/matheusmarnt/tall-icon-picker/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/matheusmarnt/tall-icon-picker/releases/tag/v1.0.0
