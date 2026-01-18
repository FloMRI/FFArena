<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ChampionDto;
use Illuminate\Support\Facades\Redis;

final readonly class ChampionService
{
    /**
     * @return array<ChampionDto>
     */
    public function getAll(): array
    {
        $champions = [];
        $rawData = Redis::hgetall('champions');

        foreach ($rawData as $key => $data) {
            /** @var array{name: string, image: string, tags: array<int, string>} $decoded */
            $decoded = json_decode($data, true);

            $champions[$key] = ChampionDto::mapToChampion($decoded);
        }

        ksort($champions);

        return $champions;
    }
}
