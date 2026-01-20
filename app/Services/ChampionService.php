<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ChampionDto;
use Exception;
use Illuminate\Support\Facades\Redis;
use Throwable;

final readonly class ChampionService
{
    /**
     * @return array<ChampionDto>
     *
     * @throws Exception|Throwable
     */
    public function getAll(): array
    {
        $champions = [];
        $prefix = config('database.redis.options.prefix', '');

        throw_unless(is_string($prefix), Exception::class, 'Prefix must be a string');

        $keys = Redis::keys('champion:*');

        foreach ($keys as $fullKey) {
            $keyWithoutPrefix = str_replace($prefix, '', $fullKey);
            /** @var array{name: string, image: string, tags: string} $data */
            $data = Redis::hgetall($keyWithoutPrefix);

            $championKey = str_replace('champion:', '', $keyWithoutPrefix);

            $champions[$championKey] = ChampionDto::mapToChampion($data);
        }

        usort($champions, fn ($a, $b): int => strcmp($a->name, $b->name));

        return $champions;
    }

    /**
     * @return array<ChampionDto>
     *
     * @throws Exception|Throwable
     */
    public function search(string $query): array
    {
        if ($query === '' || $query === '0') {
            return $this->getAll();
        }

        $results = Redis::rawCommand(
            'FT.SEARCH',
            'idx:champions',
            sprintf('@name:*%s*', $query),
            'SORTBY', 'name', 'ASC',
            'LIMIT', '0', '50'
        );

        if (! is_array($results)) {
            return [];
        }

        return $this->parseSearchResults($results);
    }

    /**
     * @param  array<mixed, mixed>  $results
     * @return array<ChampionDto>
     *
     * @throws Throwable
     */
    private function parseSearchResults(array $results): array
    {
        $champions = [];
        $counter = count($results);

        for ($i = 1; $i < $counter; $i += 2) {
            if (! isset($results[$i + 1])) {
                continue;
            }

            if (! is_array($results[$i + 1])) {
                continue;
            }

            /** @var array<int, string> $rawData */
            $rawData = $results[$i + 1];

            $data = [];
            for ($j = 0; $j < count($rawData); $j += 2) {
                if (isset($rawData[$j], $rawData[$j + 1])) {
                    $data[$rawData[$j]] = $rawData[$j + 1];
                }
            }

            /** @var array{name: string, image: string, tags: string} $data */
            $champions[] = ChampionDto::mapToChampion($data);
        }

        return $champions;
    }
}
