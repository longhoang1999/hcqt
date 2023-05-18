<?php

namespace App\Imports;

use App\Models\UnitImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataStudent implements ToModel,WithHeadingRow
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
                'msv'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'ho'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'ten' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'ntns' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'gioitinh' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'email' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'phone' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'cccd' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'lop' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'xa' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'huyen' =>  $dataExport[11] != null ? $dataExport[11] : "",
                'tinh' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'dantoc' =>  $dataExport[13] != null ? $dataExport[13] : "",
                'quoctich' =>  $dataExport[14] != null ? $dataExport[14] : "",
                'tennganh' =>  $dataExport[15] != null ? $dataExport[15] : "",
                'manganh' =>  $dataExport[16] != null ? $dataExport[16] : "",
                'manganhts' =>  $dataExport[17] != null ? $dataExport[17] : "",
                'kqxhtnam1' =>  $dataExport[18] != null ? $dataExport[18] : "",
                'kqxhtnam2' =>  $dataExport[19] != null ? $dataExport[19] : "",
                'kqxhtnam3' =>  $dataExport[20] != null ? $dataExport[20] : "",
                'kqxhtnam4' =>  $dataExport[21] != null ? $dataExport[21] : "",
                'kqxhtnam5' =>  $dataExport[22] != null ? $dataExport[22] : "",
                'nbdck' =>  $dataExport[23] != null ? $dataExport[23] : "",
                'nktkh' =>  $dataExport[24] != null ? $dataExport[24] : "",
                'trangthai' =>  $dataExport[25] != null ? $dataExport[25] : "",
                'namnh' =>  $dataExport[26] != null ? $dataExport[26] : "",
                'namtn' =>  $dataExport[27] != null ? $dataExport[27] : "",
                'namqd' =>  $dataExport[28] != null ? $dataExport[28] : "",
                'namnb' =>  $dataExport[29] != null ? $dataExport[29] : "",
                'baocaobo' =>  $dataExport[30] != null ? $dataExport[30] : "",
                'trinhdo' =>  $dataExport[31] != null ? $dataExport[31] : "",
                'namdulien' =>  $dataExport[32] != null ? $dataExport[32] : "",

                
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
