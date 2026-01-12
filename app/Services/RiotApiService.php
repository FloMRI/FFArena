<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final readonly class RiotApiService
{
    public function __construct(private RiotDragonUrlBuilder $urlBuilder) {}

    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public function getLatestVersion(): string
    {
        $urlVersion = $this->urlBuilder->getVersionsUrl();

        $versionResponse = Http::get($urlVersion);
        throw_unless($versionResponse->successful(), RuntimeException::class, 'Failed to fetch versions');

        /** @var array<int, string> $version */
        $version = $versionResponse->json();
        $version = $version[0];

        // @todo change the static latest version to bdd call
        throw_if($version !== '16.1.1', RuntimeException::class, 'Wrong version fetched');

        return $version;
    }

    public function getPatch(string $version): string
    {
        $this->downloadFile($this->urlBuilder->getPatchUrl($version), $version);

        return $this->urlBuilder->getPatchUrl($version);
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    private function downloadFile(string $filePath, string $version): void
    {
        if (! Storage::exists('patch')) {
            Storage::makeDirectory('patch');
        }

        $response = Http::timeout(300)
            ->withOptions([
                'sink' => Storage::path('patch/v.'.$version.'.tgz'),
            ])->get($filePath);

        throw_unless($response->successful(), RuntimeException::class, 'Failed to download file');
    }
}
