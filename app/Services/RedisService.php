<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ChampionDto;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final readonly class RedisService
{
    private string $jsonPath;

    /** @var array<string, mixed> */
    private array $json;

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
    public function setChampions(): void
    {
        /** @var array<string, array{name: string, image: array{full: string}, tags: array<int, string>}> $data */
        $data = $this->json['data'];

        foreach ($data as $key => $value) {
            $champion = ChampionDto::mapToChampionData($value);

            Redis::hset('champions', $key, json_encode([
                'name' => $champion->name,
                'image' => $champion->imagePath,
                'tags' => $champion->tags,
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
     * @return array<string, mixed>
     *
     * @throws Throwable
     */
    private function readJson(): array
    {
        throw_if(! File::exists($this->jsonPath), RuntimeException::class, 'Json file not found');

        $content = File::get($this->jsonPath);
        $decoded = json_decode($content, true);

        throw_if(
            ! is_array($decoded),
            RuntimeException::class,
            'Invalid JSON: expected array'
        );

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
