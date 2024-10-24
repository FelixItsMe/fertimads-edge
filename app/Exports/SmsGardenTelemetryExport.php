<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SmsGardenTelemetryExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    public function __construct(protected Collection $smsTelemetries)
    {
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->smsTelemetries;
    }

    public function map($row): array
    {
        return [
            $row->latitude,
            $row->longitude,
            $row->samples->n,
            $row->samples->p,
            $row->samples->k,
            $row->samples->ec,
            $row->samples->ph,
            $row->samples->ambient_temperature,
            $row->samples->ambient_humidity,
            $row->samples->soil_temperature,
            $row->samples->soil_moisture,
        ];
    }

    public function headings(): array
    {
        return [
            'Latitude',
            'Longitude',
            'Nitrogen (mg/kg)',
            'Fosfor (mg/kg)',
            'Kalium (mg/kg)',
            'EC (uS/cm)',
            'pH',
            'Suhu Lingkungan (°C)',
            'Kelembapan Lingkungan (%)',
            'Suhu Tanah (°C)',
            'Kelembapan Tanah (%)',
        ];
    }
}
