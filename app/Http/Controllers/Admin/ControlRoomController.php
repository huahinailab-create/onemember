<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ControlRoomService;
use Illuminate\View\View;

/** ADMIN-002 — OneMember Control Room (internal, admin-only). */
class ControlRoomController extends Controller
{
    public function index(ControlRoomService $service): View
    {
        return view('admin.control-room', [
            'internal'     => $service->internal(),
            'external'     => $service->external(),
            'featureFlags' => $service->featureFlags(),
            'warnings'     => $service->warnings(),
            'checkedAt'    => now(),
        ]);
    }
}
