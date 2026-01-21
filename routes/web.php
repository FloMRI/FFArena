<?php

declare(strict_types=1);

use App\Http\Controllers\ChampionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChampionController::class, 'showAll']);
Route::post('/', [ChampionController::class, 'search']);
