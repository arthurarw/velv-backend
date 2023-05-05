<?php

namespace App\Imports;

use App\Models\Server;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class ServersImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Server::create([
                'model' => $row[0],
                'ram' => $row[1],
                'storage' => $row[2],
                'location' => $row[3],
                'price' => $row[4],
            ]);
        }
    }
}
