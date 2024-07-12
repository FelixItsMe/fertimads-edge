<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Garden\StoreGardenRequest;
use App\Http\Requests\Garden\UpdateGardenRequest;
use App\Models\Commodity;
use App\Models\Device;
use App\Models\DeviceSelenoid;
use App\Models\Garden;
use App\Models\Land;
use App\Services\GardenService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            ->orderBy('name')
            ->paginate('10');

        $sumArea = Garden::query()
            ->sum('area');

        return view('pages.garden.index', compact('gardens', 'sumArea'));
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
                'deviceSelenoids' => function($query){
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
            'device:id,series',
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
                'deviceSelenoids' => function(Builder $query)use($garden){
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

        return redirect()->route('garden.index')->with('garden-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Garden $garden): JsonResponse
    {
        $garden->delete();

        session()->flash('garden-success', 'Berhasil dihapus!');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
