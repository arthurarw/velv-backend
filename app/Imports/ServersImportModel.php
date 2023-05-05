<?php

namespace App\Imports;

use App\Models\Server;
use Maatwebsite\Excel\Concerns\ToModel;

class ServersImportModel implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Server([
            'model' => $row[0],
            'ram' => $row[1]
        ]);
    }
}
