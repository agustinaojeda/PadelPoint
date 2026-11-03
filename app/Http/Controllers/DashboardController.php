<?php

namespace App\Http\Controllers;

use App\Queries\Dashboard\GetDashboardOverview;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(GetDashboardOverview $getDashboardOverview): Response
    {
        return Inertia::render('admin/dashboard/index', $getDashboardOverview());
    }
}
