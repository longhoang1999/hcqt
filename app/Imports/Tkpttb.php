<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Tkpttb implements ToModel,WithHeadingRow
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
                'tplgd'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'soluong'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'dientichxd'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'doituong' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'danhmuc' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'sohuu'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'lienket' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'thue'    =>  $dataExport[8] != null ? $dataExport[8] : "",
                'sophong'  =>  $dataExport[9] != null ? $dataExport[9] : "",
                'dientich'  =>  $dataExport[10] != null ? $dataExport[10] : "",
                'sophong1'  =>  $dataExport[11] != null ? $dataExport[11] : "",
                'dientich1' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'sophong2'  =>  $dataExport[13] != null ? $dataExport[13] : "",
                'dientich2' =>  $dataExport[14] != null ? $dataExport[14] : "",
                'loaiphong' =>  $dataExport[15] != null ? $dataExport[15] : "",
              
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
