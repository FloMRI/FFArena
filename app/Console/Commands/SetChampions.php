<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\RedisService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

final class SetChampions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-champions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read the json of last patch and full the redis info of the champions.';

    public function __construct(private readonly RedisService $redisService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(): int
    {
        $this->redisService->setChampions();
        $this->redisService->createSearchIndex();

        return CommandAlias::SUCCESS;
    }
}
