<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class ChampionDto
{
    /** @param array<mixed, mixed> $tags */
    private function __construct(
        public string $name,
        public string $imagePath,
        public array $tags
    ) {}

    /** @param array{name: string, image: array{full: string}, tags: array<int, string>} $data */
    public static function mapToChampionData(array $data): self
    {
        return new self(
            name: $data['name'],
            imagePath: $data['image']['full'],
            tags: $data['tags'],
        );
    }

    /** @param array{name: string, image: string, tags: array<int, string>} $data */
    public static function mapToChampion(array $data): self
    {
        return new self(
            name: $data['name'],
            imagePath: $data['image'],
            tags: $data['tags'],
        );
    }
}
