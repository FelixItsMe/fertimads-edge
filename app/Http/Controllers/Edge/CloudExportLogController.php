<?php

namespace App\Http\Controllers\Edge;

use App\Http\Controllers\Controller;
use App\Models\CloudExportLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CloudExportLogController extends Controller
{
    public function index() : View {
        $cloudExportLogs = CloudExportLog::query()
            ->latest()
            ->paginate(10);

        return view('pages.edge.cloud-export-log.index', compact('cloudExportLogs'));
    }
}
