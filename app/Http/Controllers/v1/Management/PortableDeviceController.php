<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\PortableDevice\StorePortableDeviceRequest;
use App\Http\Requests\Management\PortableDevice\UpdatePortableDeviceRequest;
use App\Models\PortableDevice;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortableDeviceController extends Controller
{
    protected string $imagePath = 'fertimads/images/portable-devices/';

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
        $portableDevices = PortableDevice::query()
            ->orderBy('series')
            ->paginate(9);

        return view('pages.management.portable-device.index', compact('portableDevices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::query()
            ->whereNot('role', 'su')
            ->pluck('name', 'id');

        return view('pages.management.portable-device.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePortableDeviceRequest $request): RedirectResponse
    {
        $image = $this->imageService->image_intervention(
            $request->safe()->image,
            $this->imagePath,
            1/1
        );

        PortableDevice::create(
            $request->safe()->except(['image']) + [
                'image' => $image
            ]
        );

        return redirect()
            ->route('portable-device.index')
            ->with('portable-device-success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PortableDevice $portableDevice): View
    {
        return view('pages.management.portable-device.show', compact('portableDevice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PortableDevice $portableDevice): View
    {
        return view('pages.management.portable-device.edit', compact('portableDevice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePortableDeviceRequest $request, PortableDevice $portableDevice): RedirectResponse
    {
        $image = $portableDevice->image;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention(
                $request->safe()->image,
                $this->imagePath,
                1/1
            );

            $this->imageService->deleteImage($portableDevice->image);
        }

        $portableDevice->update(
            $request->safe()->except(['image']) + [
                'image' => $image,
            ]
        );

        return redirect()
            ->route('portable-device.index')
            ->with('portable-device-success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PortableDevice $portableDevice): JsonResponse
    {
        $this->imageService->deleteImage($portableDevice->image);

        $portableDevice->delete();

        session()->flash('portable-device-success', 'Berhasil dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
