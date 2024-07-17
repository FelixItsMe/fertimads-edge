<?php

namespace App\Enums;

enum UserRoleEnums:string
{
    case MANAGEMENT = 'management';
    case CONTROL = 'control';
    case CARE = 'care';

    public function getLabelText()
    {
        return match ($this) {
            self::MANAGEMENT => 'Manajemen',
            self::CONTROL => 'Control',
            self::CARE => 'Care',
        };
    }
}
