<?php

namespace App\Imports;

use App\Models\UnitImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitImportEx implements ToModel,WithHeadingRow
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
            'madv'      =>$dataExport[0] != null ? $dataExport[0] : "",
            'tendv'     =>$dataExport[1] != null ? $dataExport[1] : "",
            'tenngan'   =>$dataExport[2] != null ? $dataExport[2] : "",
            'diachi'    =>$dataExport[3] != null ? $dataExport[3] : "",
            'mota'      =>$dataExport[4] != null ? $dataExport[4] : "",
            'lvhd'      =>$dataExport[5] != null ? $dataExport[5] : "",
            'lhcsgd'    =>$dataExport[6] != null ? $dataExport[6] : "",
            'truondv'   =>$dataExport[7] != null ? $dataExport[7] : "",
            'cbdbcl'    =>$dataExport[8] != null ? $dataExport[8] : "",
            'dvcc'      =>$dataExport[9] != null ? $dataExport[9] : "",
            'tenta'     =>$dataExport[10] != null ? $dataExport[10] : "",
            'tendvc'    =>$dataExport[11] != null ? $dataExport[11] : "",
            'loaidv'    =>$dataExport[12] != null ? $dataExport[12] : "",
            'dienthoai' =>$dataExport[13] != null ? $dataExport[13] : "",
            'email'     =>$dataExport[14] != null ? $dataExport[14] : "",
            'website'   =>$dataExport[15] != null ? $dataExport[15] : "",
            'namtl'     =>$dataExport[16] != null ? $dataExport[16] : "",
            'nambd'     =>$dataExport[17] != null ? $dataExport[17] : "",
            'namcb'     =>$dataExport[18] != null ? $dataExport[18] : "",


        );
        array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
