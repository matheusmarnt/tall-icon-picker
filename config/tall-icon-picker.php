<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Icon Libraries
    |--------------------------------------------------------------------------
    |
    | Define the icon libraries you want to support. Each library should have
    | a unique prefix, a package name, a path to the icons, and a label.
    |
    | Example:
    |
    | 'libraries' => [
    |    'lucide' => ['package' => 'mallardduck/blade-lucide-icons', 'path' => 'resources/svg', 'label' => 'Lucide'],
    |    'heroicons' => ['package' => 'stijnvdk/blade-heroicons', 'path' => 'resources/svg', 'label' => 'Heroicons'],
    | ]
    |
    */
    'libraries' => [
        // Popular / Modern UI
        'lucide'    => ['package' => 'mallardduck/blade-lucide-icons',      'path' => 'resources/svg',         'label' => 'Lucide'],
        'heroicons' => ['package' => 'stijnvdk/blade-heroicons',            'path' => 'resources/svg',         'label' => 'Heroicons'],
        'tabler'    => ['package' => 'secondnetwork/blade-tabler-icons',    'path' => 'resources/svg',         'label' => 'Tabler'],
        'phosphor'  => ['package' => 'andres-michel/blade-phosphor-icons',  'path' => 'resources/svg',         'label' => 'Phosphor'],
        'feather'   => ['package' => 'brunocfalcao/blade-feather-icons',    'path' => 'resources/svg',         'label' => 'Feather'],
        
        // Giants / Corporations
        'hugeicons' => ['package' => 'afatmustafa/blade-hugeicons',         'path' => 'resources/svg',         'label' => 'Hugeicons'],
        'fluentui'  => ['package' => 'codeat3/blade-fluentui-system-icons', 'path' => 'resources/svg',         'label' => 'Fluent UI'],
        'mdi'       => ['package' => 'davidhsianturi/blade-material-design-icons', 'path' => 'resources/svg',  'label' => 'Material Design'],
        'bootstrap' => ['package' => 'davidhsianturi/blade-bootstrap-icons','path' => 'resources/svg',         'label' => 'Bootstrap'],
        
        // Thematic / Specific
        'solar'     => ['package' => 'codeat3/blade-solar-icons',           'path' => 'resources/svg',         'label' => 'Solar'],
        'gravityui' => ['package' => 'codeat3/blade-gravity-ui-icons',      'path' => 'resources/svg',         'label' => 'Gravity UI'],
        'grommet'   => ['package' => 'codeat3/blade-grommet-icons',         'path' => 'resources/svg',         'label' => 'Grommet'],
        
        // FontAwesome (Separated by styles)
        'fab'       => ['package' => 'owenvoke/blade-fontawesome',          'path' => 'resources/svg/brands',  'label' => 'FA Brands'],
        'far'       => ['package' => 'owenvoke/blade-fontawesome',          'path' => 'resources/svg/regular', 'label' => 'FA Regular'],
        'fas'       => ['package' => 'owenvoke/blade-fontawesome',          'path' => 'resources/svg/solid',   'label' => 'FA Solid'],
    ],
];