<?php

namespace App\Http\Controllers\v1\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\RegionCode\StoreRegionCodeRequest;
use App\Imports\RegionCodeImport;
use App\Models\RegionCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class RegionCodeController extends Controller
{
    public function index(): View
    {
        $regionCodes = RegionCode::query()
            ->whereNull('level_2')
            ->whereNull('level_3')
            ->whereNull('level_4')
            ->oldest('level_1')
            ->get();

        return view('pages.setting.region-code.index', compact('regionCodes'));
    }

    public function create(): View
    {
        return view('pages.setting.region-code.create');
    }

    public function store(StoreRegionCodeRequest $request): RedirectResponse
    {
        RegionCode::query()->truncate();

        Excel::import(new RegionCodeImport, $request->file('csv_file'), null, \Maatwebsite\Excel\Excel::CSV);

        return redirect()
            ->route('region-code.index')
            ->with('success', 'Berhasil disimpan');
    }

    public function show($regionCode) : View {
        $split_code = explode(".", $regionCode);
        $prev_code = null;

        if (count($split_code) > 1) {
            $prev_code = '';
            $count = (count($split_code) - 1);
            for ($i=0; $i < $count; $i++) {
                $prev_code .= $split_code[$i] . (($i == ($count - 1)) ? "" : ".");
            }
        }

        $regionCodes = RegionCode::query()
            ->when((count($split_code) < 2), function(Builder $query){
                $query->whereNull('level_3');
            })
            ->when((count($split_code) < 3), function(Builder $query){
                $query->whereNull('level_4');
            })
            ->where('full_code', 'LIKE', $regionCode . "%")
            ->whereNot('full_code', $regionCode)
            ->oldest('level_1')
            ->get();

        return view('pages.setting.region-code.show', compact('regionCodes', 'regionCode', 'prev_code'));
    }
}
