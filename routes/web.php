<?php

declare(strict_types=1);

use App\Http\Controllers\ChampionController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): View => view('welcome'));
Route::get('champions', [ChampionController::class, 'showAll']);
