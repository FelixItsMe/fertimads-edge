<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function index()
    {
        $commodities = Commodity::query()
            ->latest()
            ->paginate(10);

        return response()->json($commodities);
    }
}
