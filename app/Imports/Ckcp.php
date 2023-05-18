<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Ckcp implements ToModel,WithHeadingRow
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
                'ten'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'soluong'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'mdsd' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'dtsd' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'dtsxd' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'sohuu' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'lienket' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'thue' =>  $dataExport[8] != null ? $dataExport[8] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
