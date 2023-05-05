<?php

namespace App\Http\Controllers;

use App\Tasks\RefreshServersAndLocationsTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): void
    {
        (new RefreshServersAndLocationsTask())->run();
    }
}
