<?php

namespace App\Http\Controllers\v1\Management;

use App\Exports\ToolExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\Tool\StoreToolRequest;
use App\Http\Requests\Management\Tool\UpdateToolRequest;
use App\Models\Tool;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ToolController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tools = Tool::query()
            ->when(request()->query('search'), function(Builder $query, $search){
                $search = '%' . trim($search) . '%';
                $query->whereAny([
                    'name',
                ], 'LIKE', $search);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('pages.management.tool.index', compact('tools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.management.tool.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreToolRequest $request): RedirectResponse
    {
        $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/tools/', 1/1);
        $tool = Tool::create(
            $request->safe()->except('image') +
            [
                'image' => $image
            ]
        );

        activity()
            ->performedOn($tool)
            ->event('create')
            ->log('Peralatan Baru ditambahkan');

        return redirect()->route('tool.index')->with('tool-success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tool $tool)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tool $tool): View
    {
        return view('pages.management.tool.edit', compact('tool'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateToolRequest $request, Tool $tool): RedirectResponse
    {
        $image = $tool->image;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/tools/', 1/1);

            $this->imageService->deleteImage($tool->image);
        }

        $tool->update(
            $request->safe()->except('image') +
            [
                'image' => $image
            ]
        );

        activity()
            ->performedOn($tool)
            ->event('edit')
            ->log('Peralatan diupdate');

        return redirect()->route('tool.index')->with('tool-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tool $tool): RedirectResponse|JsonResponse
    {
        $this->imageService->deleteImage($tool->image);

        $tool->delete();

        session()->flash('tool-success', 'Berhasil dihapus');

        if (request()->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus'
            ]);
        }

        activity()
            ->performedOn($tool)
            ->event('delete')
            ->log('Peralatan dihapus');

        return redirect()->route('tool.index');
    }

    public function exportExcel() : BinaryFileResponse {
        $collect = [];

        foreach (
            Tool::query()
                ->lazy() as $tool
        ) {
            $collect[] = (object) [
                "name"          => $tool->name,
                "description"   => $tool->description,
                "quantity"      => $tool->quantity,
                "created_at"    => $tool->created_at->format('Y-m-d H:i:s'),
                "updated_at"    => $tool->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        $collect = collect($collect);

        return Excel::download(new ToolExport($collect), now()->format('YmdHis') . '-peralatan.xlsx');
    }
}
