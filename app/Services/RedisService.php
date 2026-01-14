<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final readonly class RedisService
{
    private string $jsonPath;

    public function __construct()
    {
        $this->jsonPath = $this->getLatestFolder().'"/data/fr_FR/champion.json"';
    }

    /**
     * @throws Throwable
     */
    public function setNames(): void
    {
        $this->readJson();
    }

    private function getLatestFolder(): string
    {
        /** @var string $latestVersion */
        $latestVersion = Redis::get('latestVersion');

        return Storage::path('patch/latest/').$latestVersion;
    }

    /**
     * @throws Throwable
     */
    private function readJson(): mixed
    {
        throw_if(! File::exists($this->jsonPath), RuntimeException::class, 'Json file not found');

        return json_decode($this->jsonPath, true);
    }
}
