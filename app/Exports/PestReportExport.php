<?php

namespace App\Exports;

use App\Models\Pest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PestReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pest::query()
            ->with(['garden', 'commodity'])
            ->latest()
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d M Y H:i:s'),
            $row->disease_name,
            $row->pest_name,
            $row->garden->name,
            $row->commodity->name,
            $row->infected_count
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Penyakit',
            'Nama Hama',
            'Nama Kebun',
            'Nama Komoditas',
            'Total Terinfeksi'
        ];
    }
}
