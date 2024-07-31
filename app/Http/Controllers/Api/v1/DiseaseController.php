<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function index()
    {
        $diseases = Disease::query()
            ->latest()
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%'. request('search') .'%');
            })
            ->paginate(10);

        return response()->json($diseases);
    }
}
