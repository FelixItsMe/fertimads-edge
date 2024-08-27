<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ToolExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(protected Collection $tools)
    {

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->tools;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Deskripsi',
            'Jumlah',
            'Tanggal Dibuat',
            'Tanggal Diupdate',
        ];
    }
}
