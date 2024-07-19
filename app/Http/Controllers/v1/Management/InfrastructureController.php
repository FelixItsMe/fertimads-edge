<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\Infrastructure\StoreInfrastructureRequest;
use App\Http\Requests\Management\Infrastructure\UpdateInfrastructureRequest;
use App\Models\Infrastructure;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InfrastructureController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $infrastructures = Infrastructure::query()
            ->orderBy('name')
            ->paginate(10);

        return view('pages.management.infrastructure.index', compact('infrastructures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.management.infrastructure.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInfrastructureRequest $request): RedirectResponse
    {
        $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/infrastructures/', 1/1);
        Infrastructure::create(
            $request->safe()->except('image') +
            [
                'image' => $image
            ]
        );

        return redirect()->route('infrastructure.index')->with('infrastructure-success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Infrastructure $infrastructure)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Infrastructure $infrastructure): View
    {
        return view('pages.management.infrastructure.edit', compact('infrastructure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInfrastructureRequest $request, Infrastructure $infrastructure): RedirectResponse
    {
        $image = $infrastructure->image;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/infrastructures/', 1/1);

            $this->imageService->deleteImage($infrastructure->image);
        }

        $infrastructure->update(
            $request->safe()->except('image') +
            [
                'image' => $image
            ]
        );

        return redirect()->route('infrastructure.index')->with('infrastructure-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Infrastructure $infrastructure): RedirectResponse|JsonResponse
    {
        $this->imageService->deleteImage($infrastructure->image);

        $infrastructure->delete();

        session()->flash('infrastructure-success', 'Berhasil dihapus');

        if (request()->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus'
            ]);
        }

        return redirect()->route('infrastructure.index');
    }
}
