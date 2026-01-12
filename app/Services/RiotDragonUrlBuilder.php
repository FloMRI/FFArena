<?php

declare(strict_types=1);

namespace App\Services;

final readonly class RiotDragonUrlBuilder
{
    public function __construct(
        private string $baseUrl, private string $versionsUrl, private string $patchUrl
    ) {}

    public function getVersionsUrl(): string
    {
        return $this->baseUrl.$this->versionsUrl;
    }

    public function getPatchUrl(string $version): string
    {
        return sprintf(
            '%s%s',
            $this->baseUrl,
            str_replace(
                ['{version}'],
                [$version],
                $this->patchUrl
            )
        );
    }
}
