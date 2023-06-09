<?php

namespace App\Tasks;

use App\Abstract\Task;
use Exception;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\SimpleCache\InvalidArgumentException;

/**
 *
 */
class RefreshServersAndLocationsTask extends Task
{

    /**
     * @return array[]
     * @throws Exception
     */
    protected function handle(): array
    {
        try {
            $path = storage_path('/app/db/servers.xlsx');
            $reader = IOFactory::createReader('Xlsx');
            $reader = $reader->setLoadSheetsOnly('Sheet2');
            $sheet = $reader->load($path);

            $servers = $sheet->getActiveSheet()->toArray();
            unset($servers[0]);

            $quantityServersFromSpreadsheet = count($servers);
            $quantityServersFromCache = Cache::get('quantity_servers');
            if ($quantityServersFromSpreadsheet == $quantityServersFromCache) {
                $locations = Cache::get('locations');
                $servers = Cache::get('servers');
                return $this->defaultResponse($locations, $servers);
            }

            $data = [];
            $locations = [];
            $id = 1;
            foreach ($servers as $item) {
                $ram = $this->getRamSizeAndType($item[1]);
                $storage = $this->getStorageSizeAndType($item[2]);

                $data[] = [
                    'id' => $id,
                    'model' => $item[0],
                    'original_ram' => $item[1],
                    'ram' => $ram['size'],
                    'ram_type' => $ram['type'],
                    'storage' => $item[2],
                    'location' => $item[3],
                    'price' => $item[4],
                    'converted_storage_gb' => $storage['converted_storage_gb'],
                    'converted_storage_unity' => $storage['unity'],
                    'storage_type' => $storage['storage_type'],
                ];

                $id++;
                $locations[] = $item[3];
            }

            $secondsTtl = 60;
            $locations = array_unique($locations);
            $locations = array_values($locations);
            sort($locations);

            Cache::put('locations', $locations, $secondsTtl);
            Cache::put('servers', $data, $secondsTtl);
            Cache::put('quantity_servers', count($data), $secondsTtl);

            return $this->defaultResponse($locations, $data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        } catch (InvalidArgumentException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $ram
     * @return array
     */
    private function getRamSizeAndType(string $ram): array
    {
        if (empty($ram)) {
            return [];
        }

        $find = 'B';
        $position = strpos($ram, $find) + 1;
        $stringLength = strlen($ram);

        return [
            'size' => substr($ram, 0, $position),
            'type' => substr($ram, $position, $stringLength)
        ];
    }

    /**
     * @param string $storage
     * @return array
     */
    private function getStorageSizeAndType(string $storage): array
    {
        if (empty($storage)) {
            return [];
        }

        $memoryInfo = explode('x', $storage);

        $quantityStorage = $memoryInfo[0];
        $quantityStorageAndType = $memoryInfo[1];

        $position = strpos($quantityStorageAndType, 'B') + 1;
        $quantityMemory = substr($quantityStorageAndType, 0, $position);
        $storageType = str_replace($quantityMemory, '', $quantityStorageAndType);

        $quantityMemoryInt = preg_replace('/[^0-9]/', '', $quantityMemory);
        $totalMemory = intval($quantityMemoryInt) * intval($quantityStorage);
        $unityMemory = preg_replace("/[^A-Z]+/", "", $quantityMemory);
        $storageType = preg_replace("/[^A-Z]+/", "", $storageType);

        if ($unityMemory == 'TB') {
            $totalMemory = $totalMemory * 1000;
        }

        return [
            'converted_storage_gb' => $totalMemory,
            'unity' => $unityMemory,
            'storage_type' => $storageType
        ];
    }

    /**
     * @param array $locations
     * @param array $servers
     * @return array
     */
    private function defaultResponse(array $locations, array $servers): array
    {
        return [
            'locations' => $locations,
            'servers' => $servers
        ];
    }
}
