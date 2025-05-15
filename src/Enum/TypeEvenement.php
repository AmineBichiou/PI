<?php

namespace App\Enum;

enum TypeEvenement: string
{
    case FOIRE = 'foire';
    case FORMATION = 'formation';
    case CONFERENCE = 'conference';

    public function label(): string
    {
        return match ($this) {
            self::FOIRE => 'Foire',
            self::FORMATION => 'Formation',
            self::CONFERENCE => 'Conférence',
        };
    }

    public static function fromValue(string $value): ?self
    {
        return match (strtolower($value)) {
            'foire' => self::FOIRE,
            'formation' => self::FORMATION,
            'conference' => self::CONFERENCE,
            default => null,
        };
    }
}
