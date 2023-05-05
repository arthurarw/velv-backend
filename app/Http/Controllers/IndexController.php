<?php

namespace App\Http\Controllers;

use App\Imports\ServersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $path = storage_path('/app/db/servers.xlsx');
        $reader = IOFactory::createReader('Xlsx');
        $reader = $reader->setLoadSheetsOnly('Sheet2');
        $sheet = $reader->load($path);

        $servers = $sheet->getActiveSheet()->toArray();
        unset($servers[0]);

        $quantityServersFromSpreadsheet = count($servers);
        $quantityServersFromCache = Cache::get('quantity_servers');
        if ($quantityServersFromSpreadsheet == $quantityServersFromCache) {
            return response()->json([
                'success' => true,
                'data' => Cache::get('servers')
            ]);
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


        Cache::put('locations', $locations);
        Cache::put('servers', $data);
        Cache::put('quantity_servers', count($data));

        return response()->json([
            'success' => true,
            'message' => 'Nice!'
        ]);
    }

    private function getRamSizeAndType(string $ram)
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

    private function getStorageSizeAndType(string $storage)
    {
        if (empty($storage)) {
            return [];
        }

        $values = ['0', '250GB', '500GB', '1TB', '2TB', '3TB', '4TB', '8TB', '12TB', '24TB', '48TB', '72TB'];
        $memoryInfo = explode('x', $storage);

        $quantityStorage = $memoryInfo[0];
        $quantityStorageAndType = $memoryInfo[1];

        $position = strpos($quantityStorageAndType, 'B') + 1;
        $quantityMemory = substr($quantityStorageAndType, 0, $position);
        $storageType = str_replace($quantityMemory, '', $quantityStorageAndType);

        $quantityRamInt = preg_replace('/[^0-9]/', '', $quantityMemory);
        $totalMemory = intval($quantityRamInt) * intval($quantityStorage);
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
