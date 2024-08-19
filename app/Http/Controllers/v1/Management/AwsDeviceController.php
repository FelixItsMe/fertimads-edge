<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AwsDeviceController extends Controller
{
    public function index() : View {
        return view('pages.management.aws.index');
    }
}
