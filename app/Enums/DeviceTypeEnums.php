<?php

namespace App\Enums;

enum DeviceTypeEnums: string
{
    case HEAD_UNIT = 'head_unit';
    case PORTABLE = 'portable';

    public function getLabelText()
    {
        return match ($this) {
            self::HEAD_UNIT => 'Head Unit',
            self::PORTABLE => 'Portable SMS',
        };
    }
}
