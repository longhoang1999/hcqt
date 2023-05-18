<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Admissions implements ToModel,WithHeadingRow
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
                'dttg'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'loai'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'ctdt'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'stsdt' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'sttuyen' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'tlct' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'snhtt' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'dtdv' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'dtbcnhdt' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'slsvqtnh' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'hdtao' =>  $dataExport[11] != null ? $dataExport[11] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
