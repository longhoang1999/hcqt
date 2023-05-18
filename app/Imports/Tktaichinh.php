<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Tktaichinh implements ToModel,WithHeadingRow
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
                'noidung'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'n_2019'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'n_2020' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'n_2021' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'n_2022' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'n_2023' =>  $dataExport[6] != null ? $dataExport[6] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
