<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Weeds;
use Illuminate\Http\Request;

class WeedsController extends Controller
{
    public function index()
    {
        $weeds = Weeds::query()
            ->latest()
            ->paginate(10);

        return response()->json($weeds);
    }
}
