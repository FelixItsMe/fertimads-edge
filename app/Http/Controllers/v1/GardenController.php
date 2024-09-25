<?php

namespace App\Http\Controllers\v1;

use App\Exports\GardenExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Garden\StoreGardenRequest;
use App\Http\Requests\Garden\UpdateGardenRequest;
use App\Models\Commodity;
use App\Models\Device;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceFertilizeScheduleExecute;
use App\Models\DeviceSchedule;
use App\Models\DeviceScheduleExecute;
use App\Models\DeviceScheduleRun;
use App\Models\DeviceSelenoid;
use App\Models\Garden;
use App\Models\Land;
use App\Services\GardenService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GardenController extends Controller
{
    public function __construct(private GardenService $gardenService)
    {
        //...
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $gardens = Garden::query()
            ->select(['id', 'name', 'area', 'commodity_id', 'latitude', 'longitude', 'altitude', 'count_block'])
            ->with([
                'commodity:id,name'
            ])
            ->when(request()->query('search'), function (Builder $query, $search) {
                $search = '%' . trim($search) . '%';
                $query->whereAny([
                    'name',
                    'area',
                ], 'LIKE', $search);
            })
            ->orderBy('name')
            ->paginate('10');

        $sums = Garden::query()
            ->select(
                DB::raw('SUM(area) as total_area'),
                DB::raw('SUM(count_block) as total_block'),
                DB::raw('SUM(population) as total_population')
            )
            ->first();

        return view('pages.garden.index', compact('gardens', 'sums'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $lands = Land::query()
            ->pluck('name', 'id');
        $commodities = Commodity::query()
            ->pluck('name', 'id');
        $devices = Device::query()
            ->withCount([
                'deviceSelenoids' => function ($query) {
                    $query->whereNull('garden_id');
                }
            ])
            ->having('device_selenoids_count', '>', 0)
            ->pluck('series', 'id');

        return view('pages.garden.create', compact('lands', 'commodities', 'devices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGardenRequest $request): RedirectResponse
    {
        $garden = Garden::create(
            $request->safe()->except(['color', 'device_id']) + [
                'color' => substr($request->safe()->color, 1),
            ]
        );

        $this->gardenService->updateSelenoidGardenId($garden->id, $request->safe()->device_id, true);

        activity()
            ->performedOn($garden)
            ->event('create')
            ->log('Kebun baru ditambahkan');

        return redirect()->route('garden.index')->with('garden-success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Garden $garden)
    {
        $garden->load([
            'land:id,name,polygon',
            'commodity:id,name',
            'deviceSelenoid:id,garden_id,device_id,selenoid',
            'deviceSelenoid.device:id,series',
        ]);

        return view('pages.garden.show', compact('garden'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Garden $garden): View
    {
        $lands = Land::query()
            ->pluck('name', 'id');
        $commodities = Commodity::query()
            ->pluck('name', 'id');
        $devices = Device::query()
            ->withCount([
                'deviceSelenoids' => function (Builder $query) use ($garden) {
                    $query->where('garden_id', $garden->id)
                        ->orWhereNull('garden_id');
                }
            ])
            ->having('device_selenoids_count', '>', 0)
            ->pluck('series', 'id');
        $garden->load('deviceSelenoid:id,garden_id,device_id');

        return view('pages.garden.edit', compact('garden', 'lands', 'commodities', 'devices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGardenRequest $request, Garden $garden)
    {
        $garden->load('deviceSelenoid');
        $garden->update(
            $request->safe()->except(['color', 'device_id']) + [
                'color' => substr($request->safe()->color, 1),
            ]
        );

        $this->gardenService->updateSelenoidGardenId($garden->id, $request->safe()->device_id);

        activity()
            ->performedOn($garden)
            ->event('edit')
            ->log('Kebun ' . $garden->name . ' diupdate');

        return redirect()->route('garden.index')->with('garden-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Garden $garden): JsonResponse
    {
        $garden->delete();

        session()->flash('garden-success', 'Berhasil dihapus!');

        activity()
            ->performedOn($garden)
            ->event('delete')
            ->log('Kebun ' . $garden->name . ' dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }

    public function listGardensName(): JsonResponse
    {
        return response()->json([
            'message' => 'Gardens data for list',
            'gardens' => Garden::query()
                ->orderBy('name')
                ->get(['id', 'name'])
        ]);
    }

    public function gardenModal(Garden $garden): JsonResponse
    {
        $garden->load([
            'commodity:id,name',
            'pests',
            'latestPest',
            'deviceSelenoid' => ['deviceReport' => fn($query) => $query->where('type', 'like', '%pemupukan%'), 'latestReport' => fn($query) => $query->where('type', 'like', '%pemupukan%')]
        ]);

        $hasWaterSchedule = DeviceSchedule::query()
            ->active()
            ->count();

        $waterSchedule = DeviceScheduleExecute::query()
            ->whereHas('deviceScheduleRun.deviceSchedule', function ($query) use ($garden) {
                $query->where('garden_id', $garden->id);
            })
            ->whereNull('end_time')
            ->first();
        $fertilizerSchedule = DeviceFertilizeScheduleExecute::query()
            ->whereHas('deviceFertilizerSchedule.deviceSelenoid', function ($query) use ($garden) {
                $query->where('garden_id', $garden->id);
            })
            ->whereNull('execute_end')
            ->first();

        return response()->json([
            'message' => 'Data kebun untuk modal',
            'garden' => $garden,
            'telemetry' => $this->gardenService->formatedLatestTelemetry($garden),
            'hasWaterSchedule' => $hasWaterSchedule,
            'waterSchedule' => $waterSchedule,
            'fertilizerSchedule' => $fertilizerSchedule,
        ]);
    }

    public function activeWaterSchedules(Garden $garden): JsonResponse
    {
        $activeWaterSchedules = DeviceSchedule::query()
            ->with('commodity:id,name')
            ->where('garden_id', $garden->id)
            ->active()
            ->get();

        return response()->json([
            'message' => 'List jadwal penyiraman aktif',
            'activeWaterSchedules' => $activeWaterSchedules,
        ]);
    }

    public function exportExcel(): BinaryFileResponse|RedirectResponse
    {
        $collect = [];

        foreach (
            Garden::query()
                ->with([
                    'commodity:id,name',
                    'land:id,name'
                ])
                ->lazy() as $garden
        ) {
            $collect[] = (object) [
                "name"          => $garden->name,
                "area"          => $garden->area,
                "commodity"     => $garden->commodity->name,
                "latitude"      => $garden->latitude,
                "longitude"     => $garden->longitude,
                "altitude"      => $garden->altitude,
                "count_block"   => $garden->count_block,
                "popularity"    => $garden->population,
                "land"          => $garden->land->name,
                "created_at"    => $garden->created_at->format('Y-m-d H:i:s'),
                "updated_at"    => $garden->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        $collect = collect($collect);

        if ($collect->count() == 0) {
            return back()->with('garden-success', 'Tidak ada data kebun!');
        }

        return Excel::download(new GardenExport($collect), now()->format('YmdHis') . '-kebun.xlsx');
    }
}
