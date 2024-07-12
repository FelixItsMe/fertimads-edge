<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Land;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandController extends Controller
{
    public function getLandsPolygon() : JsonResponse {
        $south_west_latitude = request()->query('south_west_latitude');
        $south_west_longitude = request()->query('south_west_longitude');
        $north_east_latitude = request()->query('north_east_latitude');
        $north_east_longitude = request()->query('north_east_longitude');

        $v = Validator::make(
            [
                'south_west_latitude' => $south_west_latitude,
                'south_west_longitude' => $south_west_longitude,
                'north_east_latitude' => $north_east_latitude,
                'north_east_longitude' => $north_east_longitude,
            ], [
                'south_west_latitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
                'south_west_longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
                'north_east_latitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
                'north_east_longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            ]
        );

        if ($v->fails()) {
            return response()->json([
                'errors' => $v->errors()
            ], 422);
        }

        $lands = Land::query()
            ->select(['id', 'name', 'latitude', 'longitude', 'polygon'])
            ->with(['gardens:id,land_id,name,latitude,longitude,polygon,color'])
            ->where('latitude', '>=', $south_west_latitude)
            ->where('latitude', '<=', $north_east_latitude)
            ->where('longitude', '>=', $south_west_longitude)
            ->where('longitude', '<=', $north_east_longitude)
            ->get();

        return response()->json([
            'message' => 'Lands polygon',
            'lands' => $lands
        ]);
    }

    public function getListLandsFromDevice(Device $device) : JsonResponse {
        $lands = Land::query()
            ->whereHas('gardens.deviceSelenoid', fn($query) => $query->where('device_id', $device->id))
            ->get(['id', 'name']);

        return response()->json([
            'message' => 'Lands data for list',
            'lands' => $lands,
        ]);
    }

    public function detailLand(Land $land) : JsonResponse {
        $land->load(['gardens']);
        return response()->json([
            'land' => $land
        ]);
    }
}
