<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchServersRequest;
use App\Http\Services\ServerService;
use Illuminate\Http\JsonResponse;
use Psr\SimpleCache\InvalidArgumentException;

/**
 *
 */
class Index extends Controller
{
    /**
     * @param ServerService $service
     */
    public function __construct(private ServerService $service)
    {
    }

    /**
     * @param SearchServersRequest $request
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function __invoke(SearchServersRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->service->findAll($data);
    }
}
