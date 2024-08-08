<?php

namespace App\Http\Controllers\v1\Care;

use App\Exports\PestReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Care\StorePestRequest;
use App\Models\Commodity;
use App\Models\Disease;
use App\Models\Garden;
use App\Models\Pest;
use App\Services\GeminiService;
use App\Services\ImageService;
use App\Services\SimhashService;
use Barryvdh\DomPDF\Facade\Pdf;
use GeminiAPI\Laravel\Facades\Gemini;
use GuzzleHttp\Psr7\Request;
use Maatwebsite\Excel\Facades\Excel;

class PestController extends Controller
{
    public function __construct(private ImageService $imageService)
    {

    }

    public function index()
    {
        $pests = Pest::query()
            ->with(['garden', 'commodity'])
            ->latest()
            ->paginate(10);

        return view('pages.care.pest.index', compact('pests'));
    }

    public function create()
    {
        $gardens = Garden::all();
        $commodities = Commodity::all();

        return view('pages.care.pest.create', compact('gardens', 'commodities'));
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
            return redirect()->route('disease.show', $selectedDisease->id);
        }

        Pest::query()
            ->create([
                'disease_name' => $diseaseName,
                'pest_name' => $pestName,
                'file' => $image,
                'garden_id' => $request->garden_id,
                'commodity_Id' => $request->commodity_id,
                'infected_count' => $request->infected_count,
                'gemini_prompt' => $geminiPrompt,
                'gemini_response' => $geminiResponse
            ]);

        return redirect()->route('pest.index')->with('pest-success', 'Data berhasil disimpan!');
    }

    public function show(Pest $pest, GeminiService $service)
    {
        $response = json_decode($pest->gemini_response);

        return view('pages.care.pest.show', compact('pest', 'response'));
    }

    public function pdf()
    {
        $reports = Pest::query()
            ->with(['garden', 'commodity'])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('exports.pest-report', ['reports' => $reports])->setPaper('A4', 'landscape');

        return $pdf->stream();
    }

    public function export()
    {
        return Excel::download(new PestReportExport, 'laporan_hama.xlsx');
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
