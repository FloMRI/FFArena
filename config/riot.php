<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Riot Dragon allow to get the latest path
    |--------------------------------------------------------------------------
    */
    'base_url' => env('RIOT_DRAGON_BASE_URL'),

    'versions_url' => '/api/versions.json',

    'patch_url' => '/cdn/dragontail-{version}.tgz',
];
