<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LandExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(protected Collection $lands)
    {

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->lands;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Luas Lahan (m²)',
            'Lokasi Lahan',
            'Latitude',
            'Longitude',
            'Altitude (mdpl)',
            'Jumlah Kebun',
            'Tanggal Dibuat',
            'Tanggal Diupdate',
        ];
    }
}
