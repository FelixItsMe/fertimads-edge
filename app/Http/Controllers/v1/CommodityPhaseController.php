<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommodityPhase\StorePhaseRequest;
use App\Http\Requests\CommodityPhase\UpdatePhaseRequest;
use App\Models\Commodity;
use App\Models\CommodityPhase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CommodityPhaseController extends Controller
{
    public function create(Commodity $commodity) : View
    {
        return view('pages.commodity.phase.create', compact('commodity'));
    }

    public function store(StorePhaseRequest $request, Commodity $commodity): RedirectResponse
    {
        $phases = collect();
        $now = now();
        foreach ($request->safe()->phase as $key => $value) {
            $phases->push([
                'commodity_id' => $commodity->id,
                'phase' => $key,
                'created_at' => $now,
                ...$value
            ]);
        }

        CommodityPhase::insert($phases->all());

        return redirect()->route('commodity.show', $commodity->id)->with('commodity-success', 'Berhasil disimpan');
    }

    public function edit(Commodity $commodity) : View
    {
        $commodity->load('commodityPhases');
        return view('pages.commodity.phase.edit', compact('commodity'));
    }

    public function update(UpdatePhaseRequest $request, Commodity $commodity) : RedirectResponse
    {
        $phases = collect();
        $commodity->load('commodityPhases');
        foreach ($request->safe()->phase as $key => $value) {
            $commodity->commodityPhases[$key - 1]->age = $value['age'];
            $commodity->commodityPhases[$key - 1]->growth_phase = $value['growth_phase'];
            $commodity->commodityPhases[$key - 1]->kc = $value['kc'];
        }

        $commodity->push();

        return redirect()->route('commodity.show', $commodity->id)->with('commodity-success', 'Berhasil disimpan');
    }
}
