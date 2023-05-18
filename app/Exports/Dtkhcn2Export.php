<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class Dtkhcn2Export implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_dtkhcn2')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->nam ,
                $ts->doanh_thu,
                $ts->ty_le_doanh_thu,
                $ts->ty_so_doanh_thu,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Năm",
            "Doanh thu từ NCKH và chuyển giao công nghệ (triệu VNĐ)",
            "Tỷ lệ doanh thu từ NCKH và chuyển giao công nghệ so với tổng kinh phí đầu vào của CSGD (%)",
            "Tỷ số doanh thu từ NCKH và chuyển giao công nghệ trên cán bộ cơ hữu",
            
        ];
    }
}
