<?php

namespace App\Exports;

use App\Models\Disease;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DiseaseReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Disease::query()
            ->latest()
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d M Y H:i:s'),
            $row->name,
            $row->category,
            $row->pestisida,
            filter_var($row->works_category, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES),
            strip_tags($row->chemical),
            strip_tags($row->active_materials)
        ];
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'Nama Penyakit',
            'Kategori',
            'Jenis Pestisida',
            'Cara Kerja',
            'Golongan Senyawa Kimia',
            'Bahan Aktif'
        ];
    }
}
