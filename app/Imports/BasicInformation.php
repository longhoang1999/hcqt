<?php

namespace App\Imports;

use App\Models\UnitImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BasicInformation implements ToModel,WithHeadingRow
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
                'madv' =>  $dataExport[1] != null ? $dataExport[1] : "",
                'loaidv' =>  $dataExport[2] != null ? $dataExport[2] : "",
                'tendvTV' =>  $dataExport[3] != null ? $dataExport[3] : "",
                'tendvTA' =>  $dataExport[4] != null ? $dataExport[4] : "",
                'tenvtTV' =>  $dataExport[5] != null ? $dataExport[5] : "",
                'tenvtTA' =>  $dataExport[6] != null ? $dataExport[6] : "",
                'tenTD' =>  $dataExport[7] != null ? $dataExport[7] : "",
                'cqbcq' =>  $dataExport[8] != null ? $dataExport[8] : "",
                'ntl' =>  $dataExport[9] != null ? $dataExport[9] : "",
                'soqd' =>  $dataExport[10] != null ? $dataExport[10] : "",
                'lvhd' =>  $dataExport[11] != null ? $dataExport[11] : "",
                'diachi' =>  $dataExport[12] != null ? $dataExport[12] : "",
                'sdtlh' =>  $dataExport[13] != null ? $dataExport[13] : "",
                'fax' =>  $dataExport[14] != null ? $dataExport[14] : "",
                'email' =>  $dataExport[15] != null ? $dataExport[15] : "",
                'website' =>  $dataExport[16] != null ? $dataExport[16] : "",
                'notes' =>  $dataExport[17] != null ? $dataExport[17] : "",
                'lhcsgd' =>  $dataExport[18] != null ? $dataExport[18] : "",
                'tgdtk1' =>  $dataExport[19] != null ? $dataExport[19] : "",
                'tgcbk1' =>  $dataExport[20] != null ? $dataExport[20] : "",
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
