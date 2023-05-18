<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Ckdngv implements ToModel,WithHeadingRow
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
                'hoten'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'namsinh'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'gioitinh'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'chucdanh'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'tddt'    =>  $dataExport[5] != null ? $dataExport[5] : "",
                'cngd'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'khoinganh'    =>  $dataExport[7] != null ? $dataExport[7] : "",
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
