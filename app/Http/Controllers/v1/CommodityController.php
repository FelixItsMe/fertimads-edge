<?php

namespace App\Http\Controllers\v1;

use App\Exports\CommodityExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Commodity\StoreCommodityRequest;
use App\Http\Requests\Commodity\UpdateCommodityRequest;
use App\Models\Commodity;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CommodityController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $commodities = Commodity::query()
            ->withCount('gardens')
            ->when(request()->query('search'), function(Builder $query, $search){
                $search = '%' . trim($search) . '%';
                $query->whereAny([
                    'name',
                ], 'LIKE', $search);
            })
            ->when(request()->query('order_by'), function(Builder $query, $orderBy){
                switch ($orderBy) {
                    case 'name_asc':
                        $query->orderBy('name');
                        break;
                    case 'name_desc':
                        $query->orderByDesc('name');
                        break;
                    case 'date_asc':
                        $query->orderBy('created_at');
                        break;
                    case 'date_desc':
                        $query->orderByDesc('created_at');
                        break;

                    default:
                        $query->orderBy('name');
                        break;
                }
            })
            ->paginate(10);

        $orderBys = [
            (object) [
                'name' => 'Nama A-Z',
                'id' => 'filter-name-asc',
                'value' => 'name_asc',
            ],
            (object) [
                'name' => 'Nama Z-A',
                'id' => 'filter-name-desc',
                'value' => 'name_desc',
            ],
            (object) [
                'name' => 'Baru Ditambahkan',
                'id' => 'filter-date-asc',
                'value' => 'date_asc',
            ],
            (object) [
                'name' => 'Data Terlama',
                'id' => 'filter-date-desc',
                'value' => 'date_desc',
            ],
        ];

        return view('pages.commodity.index', compact('commodities', 'orderBys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.commodity.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommodityRequest $request): RedirectResponse
    {
        $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/commodities/', 1/1);

        $commodity = Commodity::create(
            $request->safe()->except('image') + [
                'image' => $image
            ]
        );

        activity()
            ->performedOn($commodity)
            ->event('create')
            ->log('Komoditi baru ditambahkan');

        return redirect()->route('commodity.index')->with('commodity-success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commodity $commodity): View
    {
        $commodity->load('commodityPhases');

        return view('pages.commodity.show', compact('commodity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commodity $commodity): View
    {
        return view('pages.commodity.edit', compact('commodity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommodityRequest $request, Commodity $commodity)
    {
        $image = $commodity->image;
        if ($request->safe()->image) {
            $image = $this->imageService->image_intervention($request->safe()->image, 'fertimads/images/commodities/', 1/1);

            $this->imageService->deleteImage($commodity->image);
        }

        $commodity->update(
            $request->safe()->except('image') + [
                'image' => $image
            ]
        );

        activity()
            ->performedOn($commodity)
            ->event('edit')
            ->log('Komoditi ' . $commodity->name . ' dihapus');

        return redirect()->route('commodity.index')->with('commodity-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commodity $commodity)
    {
        $this->imageService->deleteImage($commodity->image);

        $commodity->delete();

        session()->flash('commodity-success', 'Berhasil dihapus!');

        activity()
            ->performedOn($commodity)
            ->event('delete')
            ->log('Komoditi ' . $commodity->name . ' dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus!'
        ]);
    }

    public function exportExcel() : BinaryFileResponse {
        $collect = [];

        foreach (
            Commodity::query()
                ->withCount('gardens')
                ->when(request()->query('order_by'), function(Builder $query, $orderBy){
                    switch ($orderBy) {
                        case 'name_asc':
                            $query->orderBy('name');
                            break;
                        case 'name_desc':
                            $query->orderByDesc('name');
                            break;
                        case 'date_asc':
                            $query->orderBy('created_at');
                            break;
                        case 'date_desc':
                            $query->orderByDesc('created_at');
                            break;

                        default:
                            $query->orderBy('name');
                            break;
                    }
                })
                ->lazy() as $commodity
        ) {
            $collect[] = (object) [
                "name"          => $commodity->name,
                "description"   => $commodity->description,
                "gardens_count" => $commodity->gardens_count,
                "created_at"    => $commodity->created_at->format('Y-m-d H:i:s'),
                "updated_at"    => $commodity->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        $collect = collect($collect);

        return Excel::download(new CommodityExport($collect), now()->format('YmdHis') . '-komoditi.xlsx');
    }
}
