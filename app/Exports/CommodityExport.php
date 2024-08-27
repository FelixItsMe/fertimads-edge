<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommodityExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(protected Collection $commodities)
    {

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->commodities;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Deskripsi',
            'Total Kebun',
            'Tanggal Dibuat',
            'Tanggal Diupdate',
        ];
    }
}
