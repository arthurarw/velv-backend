<?php

namespace App\Tasks;

use App\Abstract\Task;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\SimpleCache\InvalidArgumentException;

/**
 *
 */
class RefreshServersAndLocationsTask extends Task
{

    /**
     * @return bool
     * @throws \Exception
     */
    protected function handle(): bool
    {
        try {
            $path = storage_path('/app/db/servers.xlsx');
            $reader = IOFactory::createReader('Xlsx');
            $reader = $reader->setLoadSheetsOnly('Sheet2');
            $sheet = $reader->load($path);

            $servers = $sheet->getActiveSheet()->toArray();
            unset($servers[0]);

            $quantityServersFromSpreadsheet = count($servers);
            $quantityServersFromCache = Cache::driver('redis')->get('quantity_servers');
            if ($quantityServersFromSpreadsheet == $quantityServersFromCache) {
                return true;
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

            $locations = array_unique($locations);

            $secondsTtl = 60;
            Cache::driver('redis')->put('locations', $locations, $secondsTtl);
            Cache::driver('redis')->put('servers', $data, $secondsTtl);
            Cache::driver('redis')->put('quantity_servers', count($data), $secondsTtl);

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        } catch (InvalidArgumentException $e) {
        }

        return true;
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
}
