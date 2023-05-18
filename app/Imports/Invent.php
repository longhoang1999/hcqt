<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Invent implements ToModel,WithHeadingRow
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
                'tpmsc'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'maso'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'loai' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'tacgia' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'cshcn' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'cshdv' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'linhvuc' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'socongnhan' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'namcap' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'noicap' =>  $dataExport[10] != null ? $dataExport[10] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
