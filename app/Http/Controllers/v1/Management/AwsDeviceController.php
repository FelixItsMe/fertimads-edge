<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\Aws\StoreAwsDeviceRequest;
use App\Http\Requests\Management\Aws\UpdateAwsDeviceRequest;
use App\Models\AwsDevice;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AwsDeviceController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    public function index() : View {
        $awsDevices = AwsDevice::query()
            ->paginate(9);

        return view('pages.management.aws.index', compact('awsDevices'));
    }

    public function create() : View {
        return view('pages.management.aws.create');
    }

    public function store(StoreAwsDeviceRequest $request) : RedirectResponse {
        $image = null;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/aws-device/', 1/1);
        }

        $device = AwsDevice::create(
            $request->safe()->except('image') + [
                'picture' => $image,
            ]
        );

        activity()
            ->performedOn($device)
            ->event('create')
            ->log('Perangkat AWS baru ditambahkan');

        return redirect()->route('aws-device.index')->with('aws-device-success', 'Berhasil disimpan!');
    }

    public function edit(AwsDevice $awsDevice) : View {
        return view('pages.management.aws.edit', compact('awsDevice'));
    }

    public function update(UpdateAwsDeviceRequest $request, AwsDevice $awsDevice) : RedirectResponse {
        $image = $awsDevice->picture;

        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/aws-device/', 1/1);

            $this->imageService->deleteImage($awsDevice->picture);
        }

        $awsDevice->update(
            $request->safe()->except('image') + [
                'picture' => $image
            ]
        );

        activity()
            ->performedOn($awsDevice)
            ->event('edit')
            ->log('Perangkat AWS ' . $awsDevice->series . ' diupdate');

        return redirect()->route('aws-device.index')->with('aws-device-success', 'Berhasil disimpan!');
    }

    public function destroy(AwsDevice $awsDevice) : JsonResponse {
        $this->imageService->deleteImage($awsDevice->picture);

        $awsDevice->delete();

        session()->flash('aws-device-success', 'Berhasil dihapus');

        activity()
            ->performedOn($awsDevice)
            ->event('delete')
            ->log('Perangkat ' . $awsDevice->series . ' dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
