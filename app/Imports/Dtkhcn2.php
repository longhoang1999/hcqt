<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Dtkhcn2 implements ToModel,WithHeadingRow
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
                'nam'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'dttkhcn'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'tldt' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'tsdt' =>  $dataExport[4] != null ? $dataExport[4] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
