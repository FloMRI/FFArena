<?php

declare(strict_types=1);

namespace App\Enums;

enum ChampionAuthorization: string
{
    case AUTHORIZED = 'authorized';
    case EXCLUDED = 'excluded';
    case UNDETERMINED = 'undetermined';
}
