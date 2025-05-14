<?php

namespace App\Enum;

enum StatutEvenement: string
{
    case A_VENIR = 'A_VENIR';
    case ANNULE = 'ANNULE';
    case TERMINE = 'TERMINE';

    public function label(): string
    {
        return match ($this) {
            self::A_VENIR => 'À venir',
            self::ANNULE => 'Annulé',
            self::TERMINE => 'Terminé',
        };
    }
}