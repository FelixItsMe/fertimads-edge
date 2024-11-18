<?php

namespace App\Http\Controllers\v1;

use App\Enums\DeviceTypeEnums;
use App\Enums\MapObjectType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceSelenoid;
use App\Models\DeviceType;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeviceController extends Controller
{
    public function __construct(
        private ImageService $imageService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $devices = Device::query()
            ->with('deviceType:id,version,name,image')
            ->when(request()->query('device_type_id'), function (Builder $query, $type) {
                $query->where('device_type_id', $type);
            })
            ->oldest('id')
            ->paginate(10);

        $deviceTypes = DeviceType::query()
            ->get(['name', 'id', 'type']);

        return view('pages.device.index', compact('devices', 'deviceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deviceType = DeviceType::query()
            ->findOrFail(request()->query('type'));

        switch ($deviceType->type->value) {
            case DeviceTypeEnums::HEAD_UNIT->value:
                return view('pages.device.create-headunit');
                break;
            case DeviceTypeEnums::PORTABLE->value:
                return view('pages.device.create-portable');
                break;

            default:
                return back()->with('failed', 'Tipe tidak diketahui!');
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        $image = null;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/device/', 1 / 1);
        }

        $deviceType = DeviceType::query()
            ->findOrFail($request->safe()->device_type_id);

        $pumps = (object) [
            'main' => 0,
            'water' => 0,
            'fertilizer_n' => 0,
            'fertilizer_p' => 0,
            'fertilizer_k' => 0,
        ];

        $is_headunit = true;

        if ($deviceType->type->value == DeviceTypeEnums::PORTABLE->value) {
            $pumps = (object) [];

            $is_headunit = false;
        }

        $device = Device::create(
            $request->safe()->except('image') + [
                'image' => $image,
                'pumps' => $pumps,
            ]
        );

        if ($is_headunit) {
            $now = now();
            $selenoids = [];
            for ($i = 1; $i <= 4; $i++) {
                $selenoids[] = [
                    'device_id'     => $device->id,
                    'selenoid'      => $i,
                    'created_at'    => $now,
                ];
            }

            DeviceSelenoid::insert($selenoids);

            $device->mapObject()->create([
                'name' => $request->series,
                'type' => MapObjectType::SAUNG_HEADUNIT->value,
                'lat' => $request->latitude,
                'lng' => $request->longitude,
                'description' => $request->note
            ]);
        }

        activity()
            ->performedOn($device)
            ->event('create')
            ->log('Perangkat baru ditambahkan');

        return redirect()->route('device.index')->with('device-success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device): View
    {
        $device->load(['deviceSelenoids.garden:id,name', 'deviceType']);

        return view('pages.device.show', compact('device'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device): View
    {
        $device->load('deviceType');

        switch ($device->deviceType->type->value) {
            case DeviceTypeEnums::HEAD_UNIT->value:
                return view('pages.device.edit-headunit', compact('device'));
                break;
            case DeviceTypeEnums::PORTABLE->value:
                return view('pages.device.edit-portable', compact('device'));
                break;

            default:
                return back()->with('failed', 'Tipe tidak diketahui!');
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
        $update = [];

        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/device/', 1 / 1);

            $this->imageService->deleteImage($device->image);
            $update = [
                'image' => $image,
            ];
        }

        $device->update(
            $request->safe()->except('image') + $update
        );

        activity()
            ->performedOn($device)
            ->event('edit')
            ->log('Perangkat ' . $device->series . ' diupdate');

        return redirect()->route('device.index')->with('device-success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device): JsonResponse
    {
        $this->imageService->deleteImage($device->image);

        $device->delete();

        session()->flash('device-success', 'Berhasil dihapus');

        activity()
            ->performedOn($device)
            ->event('delete')
            ->log('Perangkat ' . $device->series . ' dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
