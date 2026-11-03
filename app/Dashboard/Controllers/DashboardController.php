<?php

namespace App\Dashboard\Controllers;

use App\Dashboard\Queries\GetDashboardOverview;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(GetDashboardOverview $getDashboardOverview): Response
    {
        return Inertia::render('admin/dashboard/index', $getDashboardOverview());
    }
}
