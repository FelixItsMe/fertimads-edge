<?php

namespace App\Http\Controllers\v1\Control;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TelemetryRscController extends Controller
{
    public function index() : View {
        return view('pages.control.telemetry-rsc.index');
    }
}
