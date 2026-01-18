<?php

declare(strict_types=1);

namespace App\Console\Commands;

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

    public function __construct(private readonly RiotApiService $riotApiService)
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

        $this->riotApiService->getPatch($version);

        return CommandAlias::SUCCESS;
    }
}
