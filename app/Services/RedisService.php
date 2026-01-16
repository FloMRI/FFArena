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
    private mixed $json;

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->jsonPath = sprintf('%s/data/fr_FR/champion.json', $this->getLatestFolder());
        $this->json = $this->readJson();

    }

    /**
     * @throws Throwable
     */
    public function setNames(): void
    {
        foreach ($this->json['data'] as $key => $value) {
            Redis::hset('champions', $key, json_encode([
                'name' => $value['name'],
                'image' => $value['image']['full'],
                'tags' => $value['tags'],
            ]));
        }
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

        return json_decode(File::get($this->jsonPath), true);
    }
}
