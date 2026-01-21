<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ChampionDto;
use App\Enums\ChampionAuthorization;
use Exception;
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

            Redis::hset('champion:'.$key, 'name', $champion->name);
            Redis::hset('champion:'.$key, 'image', $champion->imagePath);
            Redis::hset('champion:'.$key, 'tags', implode(',', $champion->tags));
            Redis::hset('champion:'.$key, 'authorize', $this->authorizeChampion($champion)->value);
        }
    }

    /**
     * @throws Exception|Throwable
     */
    public function createSearchIndex(): void
    {
        try {
            Redis::rawCommand('FT.DROPINDEX', 'idx:champions');
        } catch (Exception) {
        }

        $prefix = config('database.redis.options.prefix', '');

        throw_unless(is_string($prefix), Exception::class, 'Prefix must be a string');

        Redis::rawCommand(
            'FT.CREATE', 'idx:champions',
            'ON', 'HASH',
            'PREFIX', '1', $prefix.'champion:',
            'SCHEMA',
            'name', 'TEXT', 'SORTABLE',
            'tags', 'TAG', 'SEPARATOR', ',',
            'authorize', 'TAG'
        );
    }

    /**
     * @throws Throwable
     */
    public function getLatestVersion(): string
    {
        $latestVersion = Redis::get('latestVersion');
        throw_if(! is_string($latestVersion), RuntimeException::class, '$latestVersion must be a string');

        return $latestVersion;
    }

    public function setLatestVersion(string $version): void
    {
        Redis::set('latestVersion', $version);
    }

    /**
     * @throws Throwable
     */
    private function getLatestFolder(): string
    {
        $latestVersion = $this->getLatestVersion();

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

    /**
     * @throws Throwable
     */
    private function authorizeChampion(ChampionDto $data): ChampionAuthorization
    {
        $jsonPath = Storage::path('championsRules.json');
        throw_if(! File::exists($jsonPath), Exception::class, 'ChampionRules.json file not found');

        $jsonFile = File::get($jsonPath);
        /** @var array{authorized: array{names: array<int, string>, tags: array<int, string>}, excluded: array{names: array<int, string>, tags: array<int, string>}} $json */
        $json = json_decode($jsonFile, true);

        $jsonAuthorized = $json['authorized'];
        $jsonExcluded = $json['excluded'];

        // Check first excluded names
        foreach ($jsonExcluded['names'] as $name) {
            if ($name === $data->name) {
                return ChampionAuthorization::EXCLUDED;
            }
        }

        // Check Authorized
        foreach ($jsonAuthorized as $key => $rule) {
            if ($key === 'names') {
                if (array_any($rule, fn ($name): bool => $name === $data->name)) {
                    return ChampionAuthorization::AUTHORIZED;
                }

                continue;
            }

            if (array_any($data->tags, fn ($tag): bool => array_any($rule, fn ($ruleTag): bool => $ruleTag === $tag))) {
                return ChampionAuthorization::AUTHORIZED;
            }
        }

        // Check excluded
        if (array_any($data->tags, fn ($tag): bool => array_any($jsonExcluded['tags'], fn ($ruleTag): bool => $ruleTag === $tag))) {
            return ChampionAuthorization::EXCLUDED;
        }

        return ChampionAuthorization::UNDETERMINED;
    }
}
