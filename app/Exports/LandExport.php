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
            'Name',
            'Address',
            'Latitude',
            'Longitude',
            'Altitude (mdpl)',
            'Area (m²)',
            'Gardens Count',
            'Created At',
            'Updated At',
        ];
    }
}
