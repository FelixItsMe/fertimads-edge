<?php

namespace App\Enums;

enum FertilizerScheduleTypeEnums: int
{
    case PEMUPUKANN = 1;
    case PEMUPUKANP = 2;
    case PEMUPUKANK = 3;

    public function getLabelText()
    {
        return match ($this) {
            self::PEMUPUKANN => 'Pemupukan N',
            self::PEMUPUKANP => 'Pemupukan P',
            self::PEMUPUKANK => 'Pemupukan K',
        };
    }

    public function getDeviceLabel()
    {
        return match ($this) {
            self::PEMUPUKANN => 'pemupukanN',
            self::PEMUPUKANP => 'pemupukanP',
            self::PEMUPUKANK => 'pemupukanK',
        };
    }
}
