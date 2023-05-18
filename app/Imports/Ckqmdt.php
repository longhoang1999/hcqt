<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Ckqmdt implements ToModel,WithHeadingRow
{
    public $data = [];
    public function model(array $row)
    {
        $dataExport = array();
        $i = 0;
        foreach ($row as $key => $value) {
            $dataExport[$i++] = $value;
        }

        if($dataExport[0] != null){
            $dataPost = (object) array(
                'stt'   =>  $dataExport[0] != null ? $dataExport[0] : "",
                'khoinganh'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'tiensi'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'thacsi' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'chinhquy' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'vlvh' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'chinhquy1' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'vlvh1' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'chinhquy2' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'vlvh2' =>  $dataExport[9] != null ? $dataExport[9] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
