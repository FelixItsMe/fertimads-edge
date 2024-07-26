<?php

namespace App\Http\Controllers\v1\Care;

use App\Http\Controllers\Controller;
use App\Http\Requests\Care\StoreDiseaseRequest;
use App\Models\Disease;
use App\Services\ImageService;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function __construct(private ImageService $imageService)
    {

    }

    public function index()
    {
        $diseases = Disease::query()
            ->latest()
            ->paginate(10);

        return view('pages.care.disease.index', compact('diseases'));
    }

    public function create()
    {
        return view('pages.care.disease.create');
    }

    public function store(StoreDiseaseRequest $request)
    {
        // Access the validated data
        $validatedData = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $filePath = $this->imageService->image_intervention($request->file('image'), 'fertimads/images/disease/', 1/1);

            // Create a new Disease record
            Disease::create($validatedData + ['image' => $filePath]);
        } else {
            Disease::create($validatedData + ['image' => '-']);
        }


        // Redirect with a success message
        return redirect()->route('disease.index')->with('success', 'Disease data has been added successfully.');
    }

    public function show(Disease $disease)
    {
        return view('pages.care.disease.show', compact('disease'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disease $disease)
    {
        $this->imageService->deleteImage($disease->image);

        $disease->delete();

        return response()->json([
            'message' => 'Berhasil dihapus!'
        ]);
    }
}
