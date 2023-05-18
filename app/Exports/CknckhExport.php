<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CknckhExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_hoat_dong_nckh')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->ten_du_an ,
                $ts->nct_ctv,
                $ts->cdttn_qt,
                $ts->tgth,
                $ts->kpth,
                $ts->tom_tat,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Tên dự án, nhiệm vụ khoa học công nghệ",
            "Người chủ trì và các thành viên",
            "Đối tác trong nước và quốc tế",
            "Thời gian thực hiện",
            "Kinh phí thực hiện (triệu đồng)",
            "Tóm tắt sản phẩm, ứng dụng thực tiễn",
        ];
    }
}
