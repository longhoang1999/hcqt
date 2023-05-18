<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Ckhnhtkh implements ToModel,WithHeadingRow
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
                'tenchude'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'tgtc'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'diadiemtc' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'sldbtd' =>  $dataExport[4] != null ? $dataExport[4] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
