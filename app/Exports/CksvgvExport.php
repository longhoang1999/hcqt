<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CksvgvExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_tyle_sv_gv')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->khoi_nganh ,
                $ts->ty_le,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Khối ngành",
            "Tỷ lệ Sinh viên/Giảng viên cơ hữu quy đổi",
            
        ];
    }
}
