<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeviceTelemetryExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    public function __construct(protected Collection $deviceTelemetries)
    {
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->deviceTelemetries;
    }

    public function map($row): array
    {
        return [
            $row->created_at,
            $row->selenoid,
            $row->N,
            $row->P,
            $row->K,
            $row->EC,
            $row->pH,
            $row->T,
            $row->H,
            $row->dhtT,
            $row->dhtH,
        ];
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'Selenoid',
            'Nitrogen',
            'Fosfor',
            'Kalium',
            'EC',
            'pH',
            'Suhu Tanah',
            'Kelembapan Tanah',
            'Suhu Lingkungan',
            'Kelembapan Lingkungan',
        ];
    }
}
