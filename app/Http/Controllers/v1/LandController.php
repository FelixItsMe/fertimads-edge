<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Land\StoreLandRequest;
use App\Http\Requests\Land\UpdateLandRequest;
use App\Models\Land;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $lands = Land::query()
            ->select(['id', 'name', 'area', 'latitude', 'longitude', 'altitude', 'address'])
            ->orderByDesc('id')
            ->paginate(10);

        $sumArea = Land::query()
            ->sum('area');

        return view('pages.land.index', compact('lands', 'sumArea'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.land.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLandRequest $request)
    {
        $validated = $request->validated();

        $land = Land::create($validated);

        activity()
            ->performedOn($land)
            ->event('create')
            ->log('Lahan baru ditambahkan');

        return redirect()->route('land.index')->with('land-success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Land $land): View
    {
        return view('pages.land.show', compact('land'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Land $land): View
    {
        return view('pages.land.edit', compact('land'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLandRequest $request, Land $land)
    {
        $validated = $request->validated();

        $land->update($validated);

        activity()
            ->performedOn($land)
            ->event('edit')
            ->log('Lahan ' . $land->name . ' diupdate');

        return redirect()->route('land.index')->with('land-success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Land $land)
    {
        $land->delete();

        session()->flash('land-success', 'Berhasil dihapus!');

        activity()
            ->performedOn($land)
            ->event('delete')
            ->log('Lahan ' . $land->name . ' dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }

    public function getLand(Land $land): JsonResponse
    {
        return response()->json([
            'land' => $land
        ]);
    }

    public function getLandPolygonWithGardens($id): JsonResponse
    {
        $land = Land::query()
            ->select(['id', 'name', 'area', 'polygon', 'latitude', 'longitude'])
            ->with([
                'gardens' => function($query){
                    $query->select(['id','land_id','polygon','color','name'])->has('deviceSelenoid');
                },
                'gardens.deviceSelenoid',
            ])
            ->findOrFail($id);

        return response()->json([
            'land' => $land
        ]);
    }

    public function landsPolyWithGardensPoly(): JsonResponse
    {
        $lands = Land::query()
            ->select(['id', 'name', 'latitude', 'longitude', 'polygon'])
            ->with([
                'gardens.commodity'
            ])
            ->get();

        return response()->json([
            'message' => 'Data Polygon lahan dan kebun',
            'lands' => $lands,
        ]);
    }
}
