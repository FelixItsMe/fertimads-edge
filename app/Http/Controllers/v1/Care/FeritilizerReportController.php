<?php

namespace App\Http\Controllers\v1\Care;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeritilizerReportController extends Controller
{
    public function index()
    {
        return view('pages.care.fertilizer-report.index');
    }
}