<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | UI Adapter
    |--------------------------------------------------------------------------
    |
    | Controls which UI component library is used to render the picker's
    | interface elements (slide panel, buttons, inputs, select).
    |
    | 'auto'        — detects TallStackUI at boot time via class_exists.
    | 'tallstackui' — always use TallStackUI v2 components (x-ts-*).
    | 'native'      — always use the built-in Alpine.js/Tailwind components.
    |
    */
    'ui' => env('TALL_ICON_PICKER_UI', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Supported Icon Libraries
    |--------------------------------------------------------------------------
    |
    | Define the icon libraries you want to support. Each library should have
    | a unique prefix, a package name, a path to the icons, and a label.
    |
    */
    'libraries' => [
        'lucide' => ['package' => 'mallardduck/blade-lucide-icons',      'path' => 'resources/svg',         'label' => 'Lucide'],
        'heroicons' => ['package' => 'blade-ui-kit/blade-heroicons',        'path' => 'resources/svg',         'label' => 'Heroicons'],
        'phosphor' => ['package' => 'codeat3/blade-phosphor-icons',        'path' => 'resources/svg',         'label' => 'Phosphor'],
        'google' => ['package' => 'codeat3/blade-google-material-design-icons', 'path' => 'resources/svg',  'label' => 'Google Material'],
        'fluent' => ['package' => 'codeat3/blade-fluentui-system-icons', 'path' => 'resources/svg',         'label' => 'Fluent UI'],
        'solar' => ['package' => 'codeat3/blade-solar-icons',           'path' => 'resources/svg',         'label' => 'Solar'],
        'hugeicons' => ['package' => 'afatmustafa/blade-hugeicons',         'path' => 'resources/svg',         'label' => 'Hugeicons'],
        'jam' => ['package' => 'codeat3/blade-jam-icons',             'path' => 'resources/svg',         'label' => 'Jam Icons'],
        'clarity' => ['package' => 'codeat3/blade-clarity-icons',         'path' => 'resources/svg',         'label' => 'Clarity'],
        'typicons' => ['package' => 'codeat3/blade-typicons',              'path' => 'resources/svg',         'label' => 'Typicons'],
        'zondicons' => ['package' => 'blade-ui-kit/blade-zondicons',        'path' => 'resources/svg',         'label' => 'Zondicons'],
        'weather' => ['package' => 'codeat3/blade-weather-icons',         'path' => 'resources/svg',         'label' => 'Weather'],
        'file' => ['package' => 'codeat3/blade-file-icons',            'path' => 'resources/svg',         'label' => 'File Icons'],

        // FontAwesome
        'fab' => ['package' => 'owenvoke/blade-fontawesome',          'path' => 'resources/svg/brands',  'label' => 'FA Brands'],
        'far' => ['package' => 'owenvoke/blade-fontawesome',          'path' => 'resources/svg/regular', 'label' => 'FA Regular'],
        'fas' => ['package' => 'owenvoke/blade-fontawesome',          'path' => 'resources/svg/solid',   'label' => 'FA Solid'],
    ],
];
