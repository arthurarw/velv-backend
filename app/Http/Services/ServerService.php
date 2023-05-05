<?php

namespace App\Http\Services;

use App\Support\Collection;
use App\Tasks\RefreshServersAndLocationsTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

/**
 *
 */
class ServerService
{
    /**
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function getLocations(): JsonResponse
    {
        $locations = Cache::driver('redis')->get('locations');
        if (empty($locations)) {
            $locations = (new RefreshServersAndLocationsTask())->run()['locations'];
        }

        return response()->json([
            'data' => $locations
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function findAll(array $data): JsonResponse
    {
        $filter = null;
        if (!empty($data)) {
            $filter = implode(';', $data);
            $servers = Cache::driver('redis')->get($filter);
            if ($servers) {
                return response()->json($servers);
            }
        }

        $servers = Cache::driver('redis')->get('servers');
        if (empty($servers)) {
            $servers = (new RefreshServersAndLocationsTask())->run()['servers'];
        }

        $servers = (new Collection($servers))->paginate($data['per_page'] ?? 15, $data['page'] ?? 1);
        if (!empty($data['storage'])) {
            $servers = $servers->where('converted_storage_gb', '<=', $data['storage']);
        }

        if (!empty($data['ram'])) {
            $servers = $servers->whereIn('ram', $data['ram']);
        }

        if (!empty($data['hard_disk_type'])) {
            $servers = $servers->where('storage_type', $data['hard_disk_type']);
        }

        if (!empty($data['location'])) {
            $servers = $servers->where('location', $data['location']);
        }

        if ($servers->isEmpty()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Servers not found'
            ], 404);
        }

        if (!empty($filter)) {
            Cache::driver('redis')->put($filter, $servers->isNotEmpty() ? $servers : [], 60);
        }

        return response()->json($servers);
    }

}
