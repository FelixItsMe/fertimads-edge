<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceType\StoreDeviceTypeRequest;
use App\Http\Requests\DeviceType\UpdateDeviceTypeRequest;
use App\Models\DeviceType;
use App\Services\DeviceTypeService;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DeviceTypeController extends Controller
{
    public function __construct(
        private ImageService $imageService,
        private DeviceTypeService $deviceTypeService,
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $deviceTypes = DeviceType::query()
            ->orderBy('id')
            ->paginate(10);

        return view('pages.device-type.index', compact('deviceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.device-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceTypeRequest $request)
    {
        $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/device-types/', 1/1);

        $description = $this->deviceTypeService->sanitizeDescription($request->safe()->description);

        DeviceType::create(
            $request->safe()->except(['image', 'description']) + [
                'image' => $image,
                'description' => $description,
            ]
        );

        return redirect()->route('device-type.index')->with('device-type-success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeviceType $deviceType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeviceType $deviceType): View
    {
        return view('pages.device-type.edit', compact('deviceType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceTypeRequest $request, DeviceType $deviceType)
    {
        $image = $deviceType->image;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/device-types/', 1/1);

            if (File::exists($deviceType->image)) {
                File::delete($deviceType->image);
            }
        }

        $description = $this->deviceTypeService->sanitizeDescription($request->safe()->description);

        $deviceType->update(
            $request->safe()->except(['image', 'description']) + [
                'image' => $image,
                'description' => $description,
            ]
        );

        return redirect()->route('device-type.index')->with('device-type-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceType $deviceType)
    {
        if (File::exists($deviceType->image)) {
            File::delete($deviceType->image);
        }

        $deviceType->delete();

        session()->flash('device-type-success', 'Berhasil disimpan');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
