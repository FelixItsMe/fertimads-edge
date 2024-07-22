<?php

namespace App\Exports;

use App\Models\DeviceReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FertilizationReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DeviceReport::query()
            ->where('type', 'like', '%pemupukan%')
            ->with('deviceSelenoid.garden')
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d M Y H:i:s'),
            '-',
            $row->deviceSelenoid->garden->name,
            $row->pemupukan_type,
            '-',
            '-',
            '-'
        ];
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'Nama Lahan',
            'Nama Kebun',
            'Jenis Pupuk Dasar',
            'Jumlah Pupuk Dasar',
            'Jenis Pupuk Susulan',
            'Jumlah Pupuk Susulan'
        ];
    }
}
