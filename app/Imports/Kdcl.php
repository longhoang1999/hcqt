<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Kdcl implements ToModel,WithHeadingRow
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
                'doituong'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'btcdg'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'namht'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'namcn' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'ttcdg' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'ndgn'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'kqhd' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'ngaycap'    =>  $dataExport[8] != null ? $dataExport[8] : "",
                'gtd' =>  $dataExport[9] != null ? $dataExport[9] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
