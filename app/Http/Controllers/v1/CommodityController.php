<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Commodity\StoreCommodityRequest;
use App\Http\Requests\Commodity\UpdateCommodityRequest;
use App\Models\Commodity;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CommodityController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $commodities = Commodity::query()
            ->withCount('gardens')
            ->orderBy('name')
            ->paginate(10);

        return view('pages.commodity.index', compact('commodities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.commodity.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommodityRequest $request): RedirectResponse
    {
        $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/commodities/', 1/1);

        Commodity::create(
            $request->safe()->except('image') + [
                'image' => $image
            ]
        );

        return redirect()->route('commodity.index')->with('commodity-success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commodity $commodity): View
    {
        $commodity->load('commodityPhases');

        return view('pages.commodity.show', compact('commodity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commodity $commodity): View
    {
        return view('pages.commodity.edit', compact('commodity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommodityRequest $request, Commodity $commodity)
    {
        $image = $commodity->image;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/commodities/', 1/1);

            $this->imageService->deleteImage($commodity->image);
        }

        $commodity->update(
            $request->safe()->except('image') + [
                'image' => $image
            ]
        );

        return redirect()->route('commodity.index')->with('commodity-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commodity $commodity)
    {
        $this->imageService->deleteImage($commodity->image);

        $commodity->delete();

        session()->flash('commodity-success', 'Berhasil dihapus!');

        return response()->json([
            'message' => 'Berhasil dihapus!'
        ]);
    }
}
