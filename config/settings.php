<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Repository
    |--------------------------------------------------------------------------
    |
    | This value determines which settings repository to use. By default the
    | package ships with database repository.
    |
    */
    'default' => env('SETTINGS_REPOSITORY_DEFAULT', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Settings Repositories
    |--------------------------------------------------------------------------
    |
    | Here are each of the settings repositories setup. You can always implmenent
    | your own repository and configure it here.
    |
    */
    'repositories' => [
        'database' => [
            'handler' => Smartisan\Settings\Repositories\DatabaseRepository::class,
            'connection' => null,
            'table' => 'settings',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings Cache
    |--------------------------------------------------------------------------
    |
    | Here you can enable or disable caching settings. By default the caching
    | is disabled. When store value is set to null, it will use the default
    | caching driver set by your application.
    |
    */
    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', false),
        'store' => null,
        'prefix' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings Casts
    |--------------------------------------------------------------------------
    |
    | Beside PHP's casting rules for other types, here you can define which
    | casts handler to use for every corresponding instances.
    |
    */
    'casts' => [
        Carbon\Carbon::class => \Smartisan\Settings\Casts\CarbonCast::class,
        Carbon\CarbonPeriod::class => \Smartisan\Settings\Casts\CarbonPeriodCast::class,
    ],

];
