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

        Land::create($validated);

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

        return redirect()->route('land.index')->with('land-success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Land $land)
    {
        //
    }

    public function getLand(Land $land) : JsonResponse {
        return response()->json([
            'land' => $land
        ]);
    }

    public function getLandPolygonWithGardens($id) : JsonResponse {
        $land = Land::query()
            ->select(['id', 'name', 'area', 'polygon', 'latitude', 'longitude'])
            ->with([
                'gardens:id,land_id,polygon,color,name',
                'gardens.deviceSelenoid:id,selenoid,garden_id',
            ])
            ->findOrFail($id);

        return response()->json([
            'land' => $land
        ]);
    }
}
