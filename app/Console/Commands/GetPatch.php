<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\RedisService;
use App\Services\RiotApiService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

final class GetPatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-patch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get and download the latest patch if needed';

    public function __construct(private readonly RiotApiService $riotApiService, private readonly RedisService $redisService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws ConnectionException|Throwable
     */
    public function handle(): int
    {
        $version = $this->riotApiService->getLatestVersion();

        $championsUrl = $this->riotApiService->getPatch($version);
        if ($championsUrl !== 'https://ddragon.leagueoflegends.com/cdn/dragontail-16.1.1.tgz') {
            return CommandAlias::FAILURE;
        }

        $this->redisService->setNames();

        return CommandAlias::SUCCESS;
    }
}
