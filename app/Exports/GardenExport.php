<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GardenExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(protected Collection $gardens)
    {

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->gardens;
    }

    public function headings(): array
    {
        return [
            'Nama Kebun',
            'Luas Kebun (m²)',
            'Nama Komoditi',
            'Latitude',
            'Longitude',
            'Altitude (mdpl)',
            'Total Block',
            'Total Populasi',
            'Nama Lahan',
            'Tanggal Dibuat',
            'Tanggal Diupdate',
        ];
    }
}
