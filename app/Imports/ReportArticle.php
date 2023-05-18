<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReportArticle implements ToModel,WithHeadingRow
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
                'tbbbc'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'maso'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'linhvuc' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'tacgia' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'donvipk' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'tapchidang' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'soissn' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'sodang' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'namdang' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'loai' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'loaitc' =>  $dataExport[11] != null ? $dataExport[11] : "",
                'dmtc' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'url' =>  $dataExport[13] != null ? $dataExport[13] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
