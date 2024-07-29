<?php

namespace App\Http\Controllers\v1\Care;

use App\Http\Controllers\Controller;
use App\Http\Requests\Care\StoreWeedsRequest;
use App\Models\Weeds;
use App\Services\ImageService;
use Illuminate\Http\Request;

class WeedsController extends Controller
{
    public function __construct(private ImageService $imageService)
    {

    }

    public function index()
    {
        $weeds = Weeds::query()
            ->latest()
            ->paginate(10);

        return view('pages.care.weeds.index', compact('weeds'));
    }

    public function create()
    {
        return view('pages.care.weeds.create');
    }

    public function store(StoreWeedsRequest $request)
    {
        // Access the validated data
        $validatedData = $request->safe()->except('foto');

        if ($request->hasFile('foto')) {
            $filePath = $this->imageService->image_intervention($request->file('foto'), 'fertimads/images/weeds/', 1/1);

            // Create a new Disease record
            Weeds::create($validatedData + ['foto' => $filePath]);
        } else {
            Weeds::create($validatedData + ['foto' => '-']);
        }


        // Redirect with a success message
        return redirect()->route('weeds.index')->with('success', 'Weeds data has been added successfully.');
    }

    public function show(Weeds $weed)
    {
        $weeds = $weed;

        return view('pages.care.weeds.show', compact('weeds'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Weeds $weed)
    {
        $weeds = $weed;

        $this->imageService->deleteImage($weeds->image);

        $weeds->delete();

        return response()->json([
            'message' => 'Berhasil dihapus!'
        ]);
    }
}
