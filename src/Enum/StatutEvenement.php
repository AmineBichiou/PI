<?php

namespace App\Enum;

enum StatutEvenement: string
{
    case A_VENIR = 'a_venir';
    case ANNULE = 'annule';
    case TERMINE = 'termine';

    public function label(): string
    {
        return match ($this) {
            self::A_VENIR => 'À venir',
            self::ANNULE => 'Annulé',
            self::TERMINE => 'Terminé',
        };
    }

    public static function fromValue(string $value): ?self
    {
        return match (strtolower($value)) {
            'a_venir' => self::A_VENIR,
            'annule' => self::ANNULE,
            'termine' => self::TERMINE,
            default => null,
        };
    }
}
