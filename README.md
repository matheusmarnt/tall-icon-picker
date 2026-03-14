<p align="center">
  <img src="resources/images/tall-icon-picker-logo.png" alt="TALL Icon Picker Logo" width="500">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-11.0%2B-red" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Livewire-3.0%2B%204.0-pink" alt="Livewire Version">
  <img src="https://img.shields.io/badge/TallStackUI-v2_optional-emerald" alt="TallStackUI Version">
</p>

<p align="center">
  A highly optimized and extensible icon picker component for Laravel applications built on the <strong>TALL Stack</strong> (TailwindCSS, Alpine.js, Livewire, Laravel). Built with a focus on <strong>Clean Architecture</strong> and performance, this package delegates rendering to the <a href="https://github.com/driesvints/blade-icons">Blade Icons</a> engine and provides a modern interface that works with or without <a href="https://tallstackui.com/">TallStackUI</a>.
</p>

---

## 🚀 Architecture & Key Features

Unlike traditional pickers that load massive arrays into memory, **TALL Icon Picker** is designed to operate with low resource consumption through a *Service Layer* pattern:

| Feature | Description |
|---|---|
| **Optimized I/O (`IconDiscoveryService`)** | SVG file scanning runs in isolation, reading artifacts directly from the `vendor` directory only when requested. |
| **Lazy Loading & Pagination** | Thousands of icons are processed on demand and paginated in the backend, keeping the browser DOM and Livewire payload extremely lightweight. |
| **Dual UI Adapter** | Automatically detects whether TallStackUI is installed and renders the appropriate components. Without TallStackUI, native Alpine.js/Tailwind components with smooth animations and modern design are activated. |
| **Extensibility (OCP)** | Open for extension via the config file (`config/tall-icon-picker.php`), allowing new icon libraries to be injected without modifying the package core. |
| **Batteries-Included** | Pre-configured for 15+ widely-used collections (Lucide, Phosphor, FontAwesome, Heroicons, etc.). |
| **i18n** | Native multi-language support. Ships with `en` and `pt_BR` — extensible by publishing the translation files. |

---

## ⚙️ Requirements

| Dependency | Version |
|---|---|
| PHP | `^8.2` |
| Laravel | `^11.0` or `^12.0` |
| Livewire | `^3.0` or `^4.0` |
| TallStackUI | `^2.0` *(optional — auto-detected)* |

---

## 📦 Installation

```bash
composer require matheusmarnt/tall-icon-picker
```

> **Note:** Composer will automatically install `blade-ui-kit/blade-icons` and all linked icon libraries. TallStackUI is a suggested dependency — if it is already installed in your project it will be used automatically; otherwise the native components will be activated.

### Updating

```bash
composer update matheusmarnt/tall-icon-picker
```

If you have previously published the config or views, re-publish them after updating to pick up any changes:

```bash
php artisan vendor:publish --tag="tall-icon-picker-config" --force
php artisan vendor:publish --tag="tall-icon-picker-views" --force
php artisan view:clear
```

---

## 🛠️ Configuration

The package works out of the box (**plug-and-play**). Publish the config file to customise the indexed libraries and the UI adapter:

```bash
php artisan vendor:publish --tag="tall-icon-picker-config"
```

The generated `config/tall-icon-picker.php` exposes two sections:

```php
return [

    /*
     | UI Adapter
     | 'auto'        — detects TallStackUI via class_exists (default)
     | 'tallstackui' — forces the x-ts-* components
     | 'native'      — forces the native Alpine.js/Tailwind components
     */
    'ui' => env('TALL_ICON_PICKER_UI', 'auto'),

    'libraries' => [
        'lucide'    => ['package' => 'mallardduck/blade-lucide-icons', 'path' => 'resources/svg', 'label' => 'Lucide'],
        'heroicons' => ['package' => 'blade-ui-kit/blade-heroicons',   'path' => 'resources/svg', 'label' => 'Heroicons'],
        // ...
    ],

];
```

To force a specific adapter via `.env`:

```dotenv
TALL_ICON_PICKER_UI=native      # always native
TALL_ICON_PICKER_UI=tallstackui # always TallStackUI
```

---

## 💻 Usage

### Via Blade Component (Recommended)

The Blade wrapper implicitly mounts the Livewire component and supports the `label` attribute:

```html
<x-tall::icon-picker
    wire:model="system_icon"
    label="Select the icon for the module"
/>
```

### Via Direct Livewire Tag

```html
<livewire:tall::icon-picker wire:model="system_icon" />
```

> **Under the Hood:** When an icon is selected, the Livewire component dispatches an `icon-picked` event mapped to the `$parentModel` property, ensuring reactive synchronisation with the parent component.

---

## 🖼️ Rendering the Selected Icon in a View

The value stored by the `wire:model` property is the **full icon identifier** in the format `{prefix}-{name}` (e.g. `lucide-home`, `heroicon-o-user`). This identifier is directly compatible with the [Blade Icons](https://blade-ui-kit.com/blade-icons) ecosystem.

### Via `<x-dynamic-component>` (Recommended)

The most idiomatic approach — renders the full SVG via Blade:

```html
{{-- $system_icon = 'lucide-home' --}}
<x-dynamic-component :component="$system_icon" class="w-6 h-6 text-gray-700" />
```

### Via `svg()` Helper

The `svg()` helper provided by `blade-ui-kit/blade-icons` returns the SVG object and allows inline rendering with `toHtml()`:

```php
// Inside a Blade component or Livewire view
{!! svg($system_icon, 'w-6 h-6 text-indigo-500')->toHtml() !!}
```

### Via `@svg` Blade Directive

```html
@svg($system_icon, 'w-6 h-6')
```

### Full Example in a Livewire Component

```php
// app/Livewire/ModuleSettings.php
class ModuleSettings extends Component
{
    public string $system_icon = '';

    public function render(): View
    {
        return view('livewire.module-settings');
    }
}
```

```html
{{-- resources/views/livewire/module-settings.blade.php --}}

{{-- Picker --}}
<x-tall::icon-picker wire:model="system_icon" label="Module icon" />

{{-- Selected icon preview --}}
@if ($system_icon)
    <div class="mt-4 flex items-center gap-2 text-sm text-gray-600">
        <x-dynamic-component :component="$system_icon" class="w-5 h-5" />
        <span>{{ $system_icon }}</span>
    </div>
@endif
```

### Displaying in Tables / Listings

```html
{{-- $record->icon = 'phosphor-house' --}}
<td class="flex items-center gap-2">
    @if ($record->icon)
        <x-dynamic-component :component="$record->icon" class="w-4 h-4 text-indigo-500" />
    @endif
    {{ $record->name }}
</td>
```

> **Note:** Make sure the icon library matching the stored icon prefix is installed in the project that will render it. Otherwise `svg()` will throw an exception. Use `@if ($icon)` as a guard before rendering.

---

## 🎨 Advanced View Customisation

Publish the views to override the picker layout or empty states:

```bash
php artisan vendor:publish --tag="tall-icon-picker-views"
```

Views are placed in `resources/views/vendor/tall`. The UI adapter components (`ui/drawer`, `ui/button`, `ui/select`, `ui/input`) are also published and can be customised individually.

### Publishing only the translations

```bash
php artisan vendor:publish --tag="tall-icon-picker-translations"
```

Language files are placed in `lang/vendor/tall-icon-picker/{locale}/icon-picker.php`.

---

## 🔧 Troubleshooting

**Newly installed icons do not appear**

```bash
php artisan view:clear
```

**SVG rendering at a disproportionate size**

The renderer applies the classes passed via `class=""`. Make sure your Tailwind utilities (`w-5 h-5`) are being compiled — add the `vendor` path to the `content` array in `tailwind.config.js` if needed.

**`x-dynamic-component` throwing `View not found`**

The Blade Icons component for that icon is not registered. Verify that the corresponding library is installed via Composer and that its `ServiceProvider` is being loaded.

**Native components without animations**

The native components use Alpine.js `x-cloak`. Add this to your global CSS:

```css
[x-cloak] { display: none !important; }
```

---

## 🤝 Contributing

Contributions are welcome! Before opening a Pull Request:

- Open an **Issue** first to discuss the bug or feature you'd like to address.
- Follow **Clean Code** guidelines and **PSR-12** formatting (enforced via [Laravel Pint](https://laravel.com/docs/12.x/pint)).
- All PRs must pass PHPStan (level 6) and the Pest test suite.
- Ensure the package passes the CI pipeline.
