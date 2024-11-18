<?php

namespace App\Http\Controllers\v1\Management;

use App\Enums\MapObjectType;
use App\Http\Controllers\Controller;
use App\Models\MapObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MapObjectController extends Controller
{
    public function index()
    {
        $mapObjects = MapObject::query()
            ->select(['id', 'name', 'lat', 'lng', 'description', 'type'])
            ->when(request()->query('search'), function (Builder $query, $search) {
                $search = '%' . trim($search) . '%';
                $query->whereAny([
                    'name',
                ], 'LIKE', $search);
            })
            ->orderBy('name')
            ->paginate('10');

        return view('pages.management.map-object.index', compact('mapObjects'));
    }

    public function create()
    {
        $objectTypes = MapObjectType::getLabelTexts();

        return view('pages.management.map-object.create', compact('objectTypes'));
    }

    public function edit(MapObject $mapObject)
    {
        $objectTypes = MapObjectType::getLabelTexts();

        return view('pages.management.map-object.edit', compact('objectTypes', 'mapObject'));
    }

    public function store(Request $request): RedirectResponse
    {
        $mapObject = MapObject::create(
            $request->validate([
                'name' => 'required',
                'type' => 'required',
                'lat' => 'required',
                'lng' => 'required',
                'description' => 'nullable'
            ])
        );

        activity()
            ->performedOn($mapObject)
            ->event('create')
            ->log('Objek peta baru ditambahkan');

        return redirect()->route('map-object.index')->with('map-object-success', 'Berhasil disimpan');
    }

    public function update(Request $request, MapObject $mapObject)
    {
        $mapObject->update(
            $request->validate([
                'name' => 'required',
                'type' => 'required',
                'lat' => 'required',
                'lng' => 'required',
                'description' => 'nullable'
            ])
        );

        activity()
            ->performedOn($mapObject)
            ->event('update')
            ->log('Objek peta di update');

        return redirect()->route('map-object.index')->with('map-object-success', 'Berhasil disimpan');
    }

    public function geoJson()
    {
        $objects = MapObject::query()
            ->get();

        $features = [];

        foreach ($objects as $object) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [floatval($object->lng), floatval($object->lat)]
                ],
                'properties' => [
                    'name' => $object->name,
                    'description' => $object->description,
                    'icon' => $object->custom_icon,
                    'type' => $object->type,
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MapObject $mapObject): JsonResponse
    {
        $mapObject->delete();

        session()->flash('map-object-success', 'Berhasil dihapus!');

        activity()
            ->performedOn($mapObject)
            ->event('delete')
            ->log($mapObject->name . ' dihapus');

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
