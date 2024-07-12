<?php

namespace App\Enums;

enum GardenSelenoidStatusEnums: int
{
    case OFF = 0;
    case ON = 1;

    public function getLabelText()
    {
        return match ($this) {
            self::OFF => 'off',
            self::ON => 'on',
        };
    }
}
