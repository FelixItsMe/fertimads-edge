<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceSelenoid;
use App\Models\DeviceType;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DeviceController extends Controller
{
    public function __construct(
        private ImageService $imageService,
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $devices = Device::query()
            ->with('deviceType:id,version,name,image')
            ->orderBy('id')
            ->paginate(10);

        return view('pages.device.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deviceTypes = DeviceType::query()
            ->pluck('name', 'id');

        return view('pages.device.create', compact('deviceTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        $image = null;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/device/', 1/1);
        }

        $device = Device::create(
            $request->safe()->except('image') + [
                'image' => $image
            ]
        );

        $now = now();
        $selenoids = [];
        for ($i=1; $i <= 4; $i++) {
            $selenoids[] = [
                'device_id'     => $device->id,
                'selenoid'      => $i,
                'created_at'    => $now,
            ];
        }

        DeviceSelenoid::insert($selenoids);

        return redirect()->route('device.index')->with('device-success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device): View
    {
        $deviceTypes = DeviceType::query()
            ->pluck('name', 'id');

        return view('pages.device.edit', compact('deviceTypes', 'device'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
        $image = $device->image;

        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/device/', 1/1);

            if (File::exists($device->image)) {
                File::delete($device->image);
            }
        }

        $device->update(
            $request->safe()->except('image') + [
                'image' => $image
            ]
        );

        return redirect()->route('device.index')->with('device-success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device): JsonResponse
    {
        if ($device->image && File::exists($device->image)) {
            File::delete($device->image);
        }

        $device->delete();

        session()->flash('device-success', 'Berhasil dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
