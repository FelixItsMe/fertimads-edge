<?php

namespace App\Enums;

enum GardenSelenoidModeEnums: string
{
    case MANUAL = 'manual';
    case AUTO = 'auto';
    case SCHEDULE = 'schedule';

    public function getLabelText()
    {
        return match ($this) {
            self::MANUAL => 'Manual',
            self::AUTO => 'Auto (Sensor)',
            self::SCHEDULE => 'Jadwal',
        };
    }
}
