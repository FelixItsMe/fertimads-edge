<?php

namespace App\Enums;

enum PhaseEnums: int
{
    case FASE1 = 1;
    case FASE2 = 2;
    case FASE3 = 3;
    case FASE4 = 4;
    case FASE5 = 5;

    public function getLabelText()
    {
        return match ($this) {
            self::FASE1 => 'Pertumbuhan Awal (init)',
            self::FASE2 => 'Pertumbuhan Awal Daun dan Akar (dev)',
            self::FASE3 => 'Pertumbuhan Batang dan Daun (mid)',
            self::FASE4 => 'Translokasi Karbohidrat ke Umbi (late)',
            self::FASE5 => 'Fase Dormansi (harvest)',
        };
    }
}
