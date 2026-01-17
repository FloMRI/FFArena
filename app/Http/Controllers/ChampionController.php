<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ChampionService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final readonly class ChampionController
{
    public function __construct(private ChampionService $championService) {}

    public function showAll(): Factory|View
    {
        $champions = $this->championService->getAll();

        return view('champions', ['champions' => $champions]);
    }
}
