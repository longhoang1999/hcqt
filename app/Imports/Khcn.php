<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Khcn implements ToModel,WithHeadingRow
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
                'tdtda'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'maso'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'loai' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'capdetai' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'namdk' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'namnghiemt' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'linhvuc' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'nganhclq' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'dvctri' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'cndtai' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'tvtgdt' =>  $dataExport[11] != null ? $dataExport[11] : "",
                'nhd' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'dvcnph' =>  $dataExport[13] != null ? $dataExport[13] : "",
                'kinhphi' =>  $dataExport[14] != null ? $dataExport[14] : "",
                'ketqua' =>  $dataExport[15] != null ? $dataExport[15] : "",
                'trangthai' =>  $dataExport[16] != null ? $dataExport[16] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
