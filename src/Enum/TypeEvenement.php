<?php

namespace App\Enum;

enum TypeEvenement: string
{
    case FOIRE = 'FOIRE';
    case FORMATION = 'FORMATION';
    case CONFERENCE = 'CONFERENCE';

    public function label(): string
    {
        return match ($this) {
            self::FOIRE => 'Foire',
            self::FORMATION => 'Formation',
            self::CONFERENCE => 'Conférence',
        };
    }
}