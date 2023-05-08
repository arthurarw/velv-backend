<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchServersRequest;
use App\Http\Services\ServerService;
use Illuminate\Http\JsonResponse;
use Psr\SimpleCache\InvalidArgumentException;

class Index extends Controller
{
    public function __construct(private ServerService $service)
    {
    }

    /**
     * Handle the incoming request.
     * @throws InvalidArgumentException
     */
    public function __invoke(SearchServersRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->service->findAll($data);
    }
}
