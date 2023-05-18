<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Nhansu implements ToModel,WithHeadingRow
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
                'stt'  =>  $dataExport[0] != null ? $dataExport[0] : "",
                'thoidiem' =>  $dataExport[1] != null ? $dataExport[1] : "",
                'hodem' =>  $dataExport[2] != null ? $dataExport[2] : "",
                'ten' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'sohieu' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'cccd' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'dvct' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'phone' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'email' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'gender' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'ngaysinh' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'quoctich' =>  $dataExport[11] != null ? $dataExport[11] : "",
                'trinhdo' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'tdnv' =>  $dataExport[13] != null ? $dataExport[13] : "",
                'namtn' =>  $dataExport[14] != null ? $dataExport[14] : "",
                'noitn' =>  $dataExport[15] != null ? $dataExport[15] : "",
                'gvsp' =>  $dataExport[16] != null ? $dataExport[16] : "",
                'qlnn' =>  $dataExport[17] != null ? $dataExport[17] : "",
                'llct' =>  $dataExport[18] != null ? $dataExport[18] : "",
                'tinhoc' =>  $dataExport[19] != null ? $dataExport[19] : "",
                'ngoaingu' =>  $dataExport[20] != null ? $dataExport[20] : "",
                'hamphong' =>  $dataExport[21] != null ? $dataExport[21] : "",
                'namphong' =>  $dataExport[22] != null ? $dataExport[22] : "",
                'cdnntd' =>  $dataExport[23] != null ? $dataExport[23] : "",
                'mscdnn' =>  $dataExport[24] != null ? $dataExport[24] : "",
                'namtd' =>  $dataExport[25] != null ? $dataExport[25] : "",
                'cdnnht' =>  $dataExport[26] != null ? $dataExport[26] : "",
                'mscdht' =>  $dataExport[27] != null ? $dataExport[27] : "",
                'cocn' =>  $dataExport[28] != null ? $dataExport[28] : "",
                'namcn' =>  $dataExport[29] != null ? $dataExport[29] : "",
                'dvsdvc' =>  $dataExport[30] != null ? $dataExport[30] : "",
                'cdctht' =>  $dataExport[31] != null ? $dataExport[31] : "",
                'tdbn' =>  $dataExport[32] != null ? $dataExport[32] : "",
                'qdbn' =>  $dataExport[33] != null ? $dataExport[33] : "",
                'cdkm' =>  $dataExport[34] != null ? $dataExport[34] : "",
                'tdiemkn' =>  $dataExport[35] != null ? $dataExport[35] : "",
                'loaihd' =>  $dataExport[36] != null ? $dataExport[36] : "",
                'shdtd' =>  $dataExport[37] != null ? $dataExport[37] : "",
                'ngcd' =>  $dataExport[38] != null ? $dataExport[38] : "",
                'tggd' =>  $dataExport[39] != null ? $dataExport[39] : "",
                'nvdpc' =>  $dataExport[40] != null ? $dataExport[40] : "",
                'loptggd' =>  $dataExport[41] != null ? $dataExport[41] : "",
                'ckhbd' =>  $dataExport[42] != null ? $dataExport[42] : "",
                'xa' =>  $dataExport[43] != null ? $dataExport[43] : "",
                'huyen' =>  $dataExport[44] != null ? $dataExport[44] : "",
                'tinh' =>  $dataExport[45] != null ? $dataExport[45] : "",
                'trangthai' =>  $dataExport[46] != null ? $dataExport[46] : "",

            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
