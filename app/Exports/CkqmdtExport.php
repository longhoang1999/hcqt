<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkqmdtExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_quymodt')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->khoi_nganh ,
                $ts->tien_si   ,
                $ts->thac_si,
                $ts->chinh_quy_dh,
                $ts->vlvh_dh ,
                $ts->chinh_quy_cd,
                $ts->vlvh_cd,
                $ts->chinh_quy_tc,
                $ts->vlvh_tc ,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Khối ngành",
            "Tiến sĩ",
            "Thạc sĩ",
            "Chính quy (Đại học)",
            "Vừa làm vừa học (Đại học)",
            "Chính quy (Cao đẳng)",
            "Vừa làm vừa học (Cao đẳng)",
            "Chính quy (Trung cấp)",
            "Vừa làm vừa học(Trung cấp)",
            
        ];
    }
}
