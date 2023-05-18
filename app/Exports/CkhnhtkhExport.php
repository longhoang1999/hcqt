<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkhnhtkhExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_hn_htkh')->get();
        
        foreach($tss as $ts){
            $time = $this->convertTime($ts->tgtc);
            $row = [
                $ts->tcd ,
                $time,
                $ts->ddtc,
                $ts->so_luong, 

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }
    public function convertTime($dateNot){
        $arr = explode('-', $dateNot);
        $dateConvert = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
        return $dateConvert;

    } 
    public function headings() :array {
        return [
            "Tên chủ đề hội nghị, hội thảo khoa học",
            "Thời gian tổ chức",
            "Địa điểm tổ chức",
            "Số lượng đại biểu tham dự",
            
        ];
    }
}
