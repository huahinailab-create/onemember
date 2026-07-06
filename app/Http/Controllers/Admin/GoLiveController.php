<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoLiveChecklistService;
use Illuminate\View\View;

class GoLiveController extends Controller
{
    public function index(GoLiveChecklistService $service): View
    {
        return view('admin.go-live', ['summary' => $service->summary()]);
    }
}
