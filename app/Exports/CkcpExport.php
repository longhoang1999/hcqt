<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkcpExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_tt_phong')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->ten,
                $ts->so_luong,
                $ts->muc_dich_su_dung,
                $ts->dtsd,
                $ts->dien_tich ,
                $ts->so_huu,
                $ts->lien_ket,
                $ts->thue,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Tên",
            "Số lượng",
            "Mục đích sử dụng",
            "Đối tượng sử dụng",
            "Diện tích sàn xây dựng (m2)",
            "Sở hữu (Hình thức sử dụng)",
            "Liên kết (Hình thức sử dụng)",
            "Thuê (Hình thức sử dụng)",
            
        ];
    }
}
