<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ChampionService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Throwable;

final readonly class ChampionController
{
    public function __construct(private ChampionService $championService) {}

    public function showAll(): Factory|View
    {
        $champions = $this->championService->getAll();

        return view('champions', ['champions' => $champions]);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function search(Request $request): Factory|View
    {
        $query = $request->get('search');
        throw_if(! is_string($query), Exception::class, 'Query must be a string');

        $champions = $this->championService->search($query);

        return view('search', ['champions' => $champions]);
    }
}
