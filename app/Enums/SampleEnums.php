<?php

namespace App\Enums;

enum SampleEnums: string
{
    case N = 'n';
    case P = 'p';
    case K = 'k';
    case PH = 'ph';
    case EC = 'ec';
    case SOIL_MOISTURE = 'soil_moisture';
    case AMBIENT_HUMIDITY = 'ambient_humidity';
    case SOIL_TEMPERATURE = 'soil_temperature';
    case AMBIENT_TEMPERATURE = 'ambient_temperature';

    public function getLabelText()
    {
        return match ($this) {
            self::N => 'N',
            self::P => 'P',
            self::K => 'K',
            self::PH => 'pH',
            self::EC => 'EC',
            self::SOIL_MOISTURE => 'Kelembapan Tanah',
            self::AMBIENT_HUMIDITY => 'Kelembapan',
            self::SOIL_TEMPERATURE => 'Suhu Tanah',
            self::AMBIENT_TEMPERATURE => 'Suhu',
        };
    }

    public function getLabelTextWithUnit()
    {
        return match ($this) {
            self::N => 'N(mg/kg)',
            self::P => 'P(mg/kg)',
            self::K => 'K(mg/kg)',
            self::PH => 'pH',
            self::EC => 'EC(uS/cm)',
            self::SOIL_MOISTURE => 'Kelembapan Tanah(%)',
            self::AMBIENT_HUMIDITY => 'Kelembapan(%)',
            self::SOIL_TEMPERATURE => 'Suhu Tanah(°C)',
            self::AMBIENT_TEMPERATURE => 'Suhu(°C)',
        };
    }
}
