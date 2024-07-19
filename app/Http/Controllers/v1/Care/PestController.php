<?php

namespace App\Http\Controllers\v1\Care;

use App\Http\Controllers\Controller;
use App\Http\Requests\Care\StorePestRequest;
use App\Models\Commodity;
use App\Models\Garden;
use App\Models\Pest;
use App\Services\GeminiService;
use App\Services\ImageService;
use GeminiAPI\Laravel\Facades\Gemini;
use GuzzleHttp\Psr7\Request;

class PestController extends Controller
{
    public function __construct(private ImageService $imageService)
    {

    }

    public function index()
    {
        $pests = Pest::query()
            ->with(['garden', 'commodity'])
            ->paginate(10);

        return view('pages.care.pest.index', compact('pests'));
    }

    public function create()
    {
        $gardens = Garden::all();
        $commodities = Commodity::all();

        return view('pages.care.pest.create', compact('gardens', 'commodities'));
    }

    public function store(StorePestRequest $request, GeminiService $service)
    {
        $image = $this->imageService->image_intervention($request->safe()->file, 'fertimads/images/pest/', 1/1);

        [$geminiResponse, $diseaseName, $pestName] = $service->generate('image/jpeg', $image, $request->gemini_prompt);

        Pest::query()
            ->create([
                'disease_name' => $diseaseName,
                'pest_name' => $pestName,
                'file' => $image,
                'garden_id' => 1,
                'commodity_Id' => $request->commodity_id,
                'infected_count' => $request->infected_count,
                'gemini_prompt' => $request->gemini_prompt,
                'gemini_response' => $geminiResponse
            ]);

        return redirect()->route('pest.index');
    }

    public function show(Pest $pest, GeminiService $service)
    {
        $response = json_decode($pest->gemini_response);

        return view('pages.care.pest.show', compact('pest', 'response'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pest $pest)
    {
        $this->imageService->deleteImage($pest->image);

        $pest->delete();

        session()->flash('pest-success', 'Berhasil dihapus!');

        return response()->json([
            'message' => 'Berhasil dihapus!'
        ]);
    }
}