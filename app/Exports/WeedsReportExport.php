<?php

namespace App\Exports;

use App\Models\Weeds;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WeedsReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Weeds::query()
            ->latest()
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d M Y H:i:s'),
            $row->nama_gulma,
            $row->klasifikasi_berdasarkan_cara_kerja,
            strip_tags($row->golongan_senyawa_kimia),
            strip_tags($row->bahan_aktif)
        ];
    }

    public function headings(): array
    {
        return [
            "Waktu",
            "Nama Gulma",
            "Cara Kerja",
            "Golongan Senyawa Kimia",
            "Bahan Aktif",
        ];
    }
}
