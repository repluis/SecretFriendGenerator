<?php

namespace App\Modules\Dashboard\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('modules.fundraising.navidad');
    }
}
