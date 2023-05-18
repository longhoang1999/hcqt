<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Chtrinhdaotao implements ToModel,WithHeadingRow
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
                'khoabm'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'cndt'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'manganh' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'tenctdt' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'lhdt' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'nambddt' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'ddtcdt' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'slsv' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'slsvtn' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'tenvb' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'tddt' =>  $dataExport[11] != null ? $dataExport[11] : "",
                'tgdtc' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'cttshn' =>  $dataExport[13] != null ? $dataExport[13] : "",
                'mkndt' =>  $dataExport[14] != null ? $dataExport[14] : "",
                'mlvdt' =>  $dataExport[15] != null ? $dataExport[15] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
