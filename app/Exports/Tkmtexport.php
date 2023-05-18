<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class Tkmtexport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_tk_mt')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->don_vi ,
                $ts->tong_so,
                $ts->so_may_moi,
                $ts->so_may_cu,
                $ts->dchtvp,
                $ts->dcht,
                $ts->ghi_chu,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Đơn vị",
            "Tổng số",
            "Số máy mới (Từ 5 năm trở lại)",
            "Số máy cũ (từ  trên 5 năm trở lên)",
            "Dùng cho hệ thống văn phòng",
            "Dùng cho học tập3",
            "Ghi chú",
            
        ];
    }
}
