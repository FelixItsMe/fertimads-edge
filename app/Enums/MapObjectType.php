<?php

namespace App\Enums;

enum MapObjectType: int
{
    case GEDUNG_PUSAT = 1;
    case EMBUNG = 2;
    case SAUNG_HEADUNIT = 3;
    case TOREN = 4;
    case SMS_FIX_STATION = 5;
    case PENANGKAL_PETIR = 6;

    public static function getLabelTexts()
    {
        return [
            1 => 'Gedung Pusat',
            2 => 'Embung',
            3 => 'Saung Head Unit',
            4 => 'Toren',
            5 => 'SMS Fix Station',
            6 => 'Penangkal Petir',
        ];
    }

    public function getLabelText()
    {
        return match ($this) {
            self::GEDUNG_PUSAT => 'Gedung Pusat',
            self::EMBUNG => 'Embung',
            self::SAUNG_HEADUNIT => 'Saung Head Unit',
            self::TOREN => 'Toren',
            self::SMS_FIX_STATION => 'SMS Fix Station',
            self::PENANGKAL_PETIR => 'Penangkal Petir',
        };
    }
}
