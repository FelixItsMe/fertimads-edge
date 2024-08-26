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
            'Name',
            'Area (m²)',
            'Latitude',
            'Longitude',
            'Altitude (mdpl)',
            'Total Block',
            'Total Population',
            'Komoditi',
            'Lahan',
            'Created At',
            'Updated At',
        ];
    }
}
