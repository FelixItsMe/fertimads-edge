<?php

namespace App\Imports;

use App\Models\DailyIrrigation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\WithUpserts;

class DailyIrrigationImport implements ToModel, WithUpserts, WithUpsertColumns
{
    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'date';
    }

    /**
     * @return array
     */
    public function upsertColumns()
    {
        return ['eto'];
    }

    public function model(array $row)
    {
        $list =  [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $year = $row[0];
        $month = array_search($row[1], $list);
        $date = $row[2];

        if (!$year) {
            return null;
        }
        if ($month === false) {
            return null;
        }
        if (!$date) {
            return null;
        }

        return new DailyIrrigation([
            'date' => $year . "-" . str_pad(($month + 1),2,"0", STR_PAD_LEFT) . "-" . $date,
            'eto' => $row[3],
        ]);
    }
}
