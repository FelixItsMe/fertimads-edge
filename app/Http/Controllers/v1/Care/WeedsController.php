<?php

namespace App\Http\Controllers\v1\Care;

use App\Exports\WeedsReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Care\StoreWeedsRequest;
use App\Models\Weeds;
use App\Services\ImageService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        return redirect()->route('weeds.index')->with('weeds-success', 'Data gulma telah berhasil ditambahkan');
    }

    public function show(Weeds $weed)
    {
        $weeds = $weed;

        return view('pages.care.weeds.show', compact('weeds'));
    }

    public function pdf()
    {
        $reports = Weeds::query()
            ->latest()
            ->get();

        $pdf = Pdf::loadView('exports.weeds-report', ['reports' => $reports])->setPaper('A4', 'landscape');

        return $pdf->stream();
    }

    public function export()
    {
        return Excel::download(new WeedsReportExport, 'laporan_gulma.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Weeds $weed)
    {
        $weeds = $weed;

        $this->imageService->deleteImage($weeds->image);

        $weeds->delete();

        session()->flash('weeds-success', 'Data Gulma Berhasil dihapus!');

        return response()->json([
            'message' => 'Berhasil dihapus!'
        ]);
    }
}
