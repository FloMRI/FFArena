<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\RiotDragonUrlBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(function (): RiotDragonUrlBuilder {
            $baseUrl = config('riot.base_url');
            $versionUrl = config('riot.versions_url');
            $patchUrl = config('riot.patch_url');

            throw_if(! is_string($baseUrl) || ($baseUrl === '' || $baseUrl === '0'), RuntimeException::class, 'RIOT_DRAGON_BASE_URL must be configured');
            throw_if(! is_string($versionUrl) || ($versionUrl === '' || $versionUrl === '0'), RuntimeException::class, 'RIOT_DRAGON_VERSION_URL must be configured');
            throw_if(! is_string($patchUrl) || ($patchUrl === '' || $patchUrl === '0'), RuntimeException::class, 'RIOT_DRAGON_PATCH_URL must be configured');

            return new RiotDragonUrlBuilder(
                baseUrl: $baseUrl,
                versionsUrl: $versionUrl,
                patchUrl: $patchUrl
            );
        });
    }

    public function boot(): void
    {
        $this->bootModelsDefaults();
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
    }
}
