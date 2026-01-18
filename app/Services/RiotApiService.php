<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final readonly class RiotApiService
{
    public function __construct(private RiotDragonUrlBuilder $urlBuilder, private RedisService $redisService) {}

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

        throw_if($version !== $this->redisService->getLatestVersion(), RuntimeException::class, 'Wrong version fetched');

        return $version;
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getPatch(string $version): void
    {
        $downloadPath = $this->downloadFile($this->urlBuilder->getPatchUrl($version), $version);
        $this->extractFile($downloadPath);
        $this->redisService->setLatestVersion($version);
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    private function downloadFile(string $filePath, string $version): string
    {
        if (! Storage::exists('patch')) {
            Storage::makeDirectory('patch');
        }

        $downloadPath = Storage::path('patch/v.'.$version.'.tgz');

        $response = Http::timeout(300)
            ->withOptions([
                'sink' => $downloadPath,
            ])->get($filePath);

        throw_unless($response->successful(), RuntimeException::class, 'Failed to download file');

        return $downloadPath;
    }

    private function extractFile(string $downloadFile): void
    {
        $extractDir = 'patch/latest';

        if (Storage::exists($extractDir)) {
            Storage::deleteDirectory($extractDir);
        }

        Storage::makeDirectory($extractDir);

        Process::run([
            'tar',
            '-xzf',
            $downloadFile,
            '-C',
            Storage::path($extractDir),
        ])->throw();

        unlink($downloadFile);
    }
}
