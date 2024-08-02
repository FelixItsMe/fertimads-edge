<?php

namespace App\Http\Controllers\Api\v1\Care;

use App\Http\Controllers\Controller;
use App\Http\Requests\Care\StorePestRequest;
use App\Models\Commodity;
use App\Models\Disease;
use App\Models\Garden;
use App\Models\Pest;
use App\Services\GeminiService;
use App\Services\ImageService;
use App\Services\SimhashService;
use Illuminate\Http\Request;

class PestController extends Controller
{
    public function __construct(private ImageService $imageService)
    {

    }

    public function index()
    {
        $pests = Pest::query()
            ->with(['garden', 'commodity'])
            ->when(request('search'), function ($query) {
                $query->where('pest_name', 'like', '%'. request('search') .'%');
            })
            ->paginate(10);

        return response()->json($pests);
    }

    public function create()
    {
        $gardens = Garden::all();
        $commodities = Commodity::all();

        return response()->json(['gardens' => $gardens, 'commodities' => $commodities]);
    }

    public function store(StorePestRequest $request, GeminiService $service, SimhashService $simhash)
    {
        $image = $this->imageService->image_intervention($request->safe()->file, 'fertimads/images/pest/', 1/1);

        [$geminiPrompt, $geminiResponse, $diseaseName, $pestName] = $service->generate('image/jpeg', $image, $request->gemini_prompt);

        $response = json_decode($geminiResponse);

        $selectedDisease = null;
        $diseases = Disease::query()
            ->get();

        foreach($diseases as $disease) {
            if ($simhash->isSimilar($response->nama_penyakit, $disease->name)) {
                $selectedDisease = $disease;
            }
        }

        if (!is_null($selectedDisease)) {
            $apiResponse = [
                'disease_name' => $diseaseName,
                'pest_name' => $pestName,
                'file' => $image,
                'garden_id' => $request->garden_id,
                'commodity_id' => $request->commodity_id,
                'infected_count' => $request->infected_count,
                'gemini_prompt' => $geminiPrompt,
                'gemini_response' => $geminiResponse,
                'gemini_response_decoded' => [
                    'nama_penyakit' => $selectedDisease->name,
                    'nama_hama' => '-',
                    'gejala' => $selectedDisease->symptoms,
                    'penyebab' => $selectedDisease->cause,
                    'pengobatan' => $selectedDisease->cure,
                    'pengendalian' => $selectedDisease->control
                ]
            ];

            return response()->json($apiResponse);
        }

        $pest = Pest::query()
            ->create([
                'disease_name' => $diseaseName,
                'pest_name' => $pestName,
                'file' => $image,
                'garden_id' => $request->garden_id,
                'commodity_id' => $request->commodity_id,
                'infected_count' => $request->infected_count,
                'gemini_prompt' => $geminiPrompt,
                'gemini_response' => $geminiResponse
            ]);

        return response()->json($pest);
    }

    public function show(Pest $pest)
    {
        $response = json_decode($pest->gemini_response);

        return response()->json(['pest' => $pest, 'gemini_response' => $response]);
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
