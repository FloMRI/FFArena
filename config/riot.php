<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Riot Dragon allow to get the latest path
    |--------------------------------------------------------------------------
    */
    'base_url' => env('RIOT_DRAGON_BASE_URL'),

    'versions_url' => env('RIOT_DRAGON_BASE_URL').'/api/versions.json',

    'champions_url' => fn (string $version): string => env('RIOT_DRAGON_BASE_URL')
        .sprintf('/cdn/%s/data/fr_FR/champion.json', $version),
];
