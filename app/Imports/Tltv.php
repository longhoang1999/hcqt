<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Tltv implements ToModel,WithHeadingRow
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
                'mhp'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'tenhp'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'khoinganh' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'ychp' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'sachin' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'sachdt' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'ychp1' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'sachin1' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'sachdt1' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'trennn' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'tnndn' =>  $dataExport[11] != null ? $dataExport[11] : "",
                'ychp4' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'sachin4' =>  $dataExport[13] != null ? $dataExport[13] : "",
                'sachdt4' =>  $dataExport[14] != null ? $dataExport[14] : "",
                'ychp5' =>  $dataExport[15] != null ? $dataExport[15] : "",
                'sachin5' =>  $dataExport[16] != null ? $dataExport[16] : "",
                'sachdt5' =>  $dataExport[17] != null ? $dataExport[17] : "",
                'sldtcbi' =>  $dataExport[18] != null ? $dataExport[18] : "",
                'sldtcdt' =>  $dataExport[19] != null ? $dataExport[19] : "",
                'trennn1' =>  $dataExport[20] != null ? $dataExport[20] : "",
                'tnndn1' =>  $dataExport[21] != null ? $dataExport[21] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
