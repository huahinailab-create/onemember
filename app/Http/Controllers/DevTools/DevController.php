<?php

namespace App\Http\Controllers\DevTools;

use App\Http\Controllers\Controller;
use App\Services\DevTools\DevAuditLogger;

abstract class DevController extends Controller
{
    public function __construct(protected DevAuditLogger $audit) {}
}
