<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Http\Services\ServerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetLocations extends Controller
{
    public function __construct(private ServerService $service)
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $this->service->getLocations();
    }
}
