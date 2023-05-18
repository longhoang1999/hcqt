<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkmhExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_monhoc')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->ten_mon ,
                $ts->mdmh,
                $ts->so_tin_chi,
                $ts->lich_day,
                $ts->ppdgsv,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Tên môn học",
            "Mục đích môn học",
            "Số tín chỉ",
            "Lịch trình giảng dạy",
            "Phương pháp đánh giá sinh viên",
            
        ];
    }
}
