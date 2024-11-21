<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\WaterPipeline\StoreWaterPipelineRequest;
use App\Http\Requests\Management\WaterPipeline\UpdateWaterPipelineRequest;
use App\Models\WaterPipeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaterPipelineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $waterPipelines = WaterPipeline::query()
            ->latest()
            ->paginate(10);

        return view('pages.management.water-pipeline.index', compact('waterPipelines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.management.water-pipeline.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterPipelineRequest $request): RedirectResponse
    {
        $waterPipeline = WaterPipeline::create($request->validated());

        activity()
            ->performedOn($waterPipeline)
            ->event('create')
            ->log('Kebun baru ditambahkan');

        return redirect()
            ->route('water-pipeline.index')
            ->with('water-pipeline-success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WaterPipeline $waterPipeline): View
    {
        return view('pages.management.water-pipeline.show', compact('waterPipeline'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WaterPipeline $waterPipeline): View
    {
        return view('pages.management.water-pipeline.edit', compact('waterPipeline'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaterPipelineRequest $request, WaterPipeline $waterPipeline)
    {
        $waterPipeline->update($request->validated());

        activity()
            ->performedOn($waterPipeline)
            ->event('update')
            ->log("Jalur pipa air " . $waterPipeline->name . " di update");

        return redirect()
            ->route('water-pipeline.show', $waterPipeline->id)
            ->with('water-pipeline-success', 'berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WaterPipeline $waterPipeline)
    {
        $waterPipeline->delete();

        session()->flash('water-pipeline-success', 'Berhasil dihapus');

        if (request()->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus'
            ]);
        }

        activity()
            ->performedOn($waterPipeline)
            ->event('delete')
            ->log("Jalur pipa air " . $waterPipeline->name . " di hapus");

        return redirect()->route('water-pipeline.index');
    }
}
