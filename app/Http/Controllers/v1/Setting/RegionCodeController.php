<?php

namespace App\Http\Controllers\v1\Setting;

use App\Events\ImportFinishEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\RegionCode\StoreRegionCodeRequest;
use App\Imports\RegionCodeImport;
use App\Jobs\ProcessImportJob;
use App\Models\RegionCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $importStatus = Cache::get('import', 0);
        if ($importStatus == 2) {
            $importStatus = 0;
            Cache::forget('import');
        }

        return view('pages.setting.region-code.create', compact('importStatus'));
    }

    public function store(StoreRegionCodeRequest $request): JsonResponse
    {
        $file = $request->file('csv_file');
        $storedFile = $file->store('csv', 'public');
        ProcessImportJob::dispatch(storage_path('app/public/' . $storedFile), Auth::user()->id)
            ->onQueue('import');
        Cache::put('import', 1, 60 * 60 * 2);
        // event(new ImportFinishEvent(Auth::user()->id));

        return response()
            ->json([
                'message' => 'Import sedang berjalan harap tunggu...'
            ]);
    }

    public function show($regionCode): View
    {
        $split_code = explode(".", $regionCode);
        $prev_code = null;

        if (count($split_code) > 1) {
            $prev_code = '';
            $count = (count($split_code) - 1);
            for ($i = 0; $i < $count; $i++) {
                $prev_code .= $split_code[$i] . (($i == ($count - 1)) ? "" : ".");
            }
        }

        $regionCodes = RegionCode::query()
            ->when((count($split_code) < 2), function (Builder $query) {
                $query->whereNull('level_3');
            })
            ->when((count($split_code) < 3), function (Builder $query) {
                $query->whereNull('level_4');
            })
            ->where('full_code', 'LIKE', $regionCode . "%")
            ->whereNot('full_code', $regionCode)
            ->oldest('level_1')
            ->get();

        return view('pages.setting.region-code.show', compact('regionCodes', 'regionCode', 'prev_code'));
    }
}
