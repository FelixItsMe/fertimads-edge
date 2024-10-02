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
        $response = json_decode($row->gemini_response);

        return [
            $row->created_at->format('d M Y H:i:s'),
            $row->disease_name,
            $row->pest_name,
            $row->garden->name,
            $response?->penyebab
                ? (is_array($response?->penyebab) ? str_replace(['["', '","', '"]'], ['', ",", ""], json_encode($response?->penyebab))
                    : $response?->penyebab)
                : '-',
            $response?->pengendalian ? (is_array($response?->pengendalian) ? str_replace(['["', '","', '"]'], ['', ", ", ""], json_encode($response?->pengendalian))
                : $response?->pengendalian)
                : '-'
        ];
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'Nama Penyakit',
            'Nama Hama',
            'Nama Kebun',
            'Penyebab',
            'Pengendalian'
        ];
    }
}
