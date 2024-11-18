<?php

namespace App\Imports;

use App\Models\RegionCode;
use Maatwebsite\Excel\Concerns\ToModel;

class RegionCodeImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $full_code = $row[0];
        $split_code = explode(".", $full_code);
        $region_name = $row[1];
        if ($region_name == null) {
            preg_match('/"(.*?)"/', $split_code[count($split_code) - 1], $matches);

            $region_name = $matches[1];

            $full_code = explode(",", $full_code)[0];
            $split_code = explode(".", $full_code);
        }

        return new RegionCode([
            'level_1' => $split_code[0],
            'level_2' => (count($split_code) < 2 ? null : $split_code[1]),
            'level_3' => (count($split_code) < 3 ? null : $split_code[2]),
            'level_4' => (count($split_code) < 4 ? null : $split_code[3]),
            'full_code' => $full_code,
            'region_name' => $region_name,
        ]);
    }
}
