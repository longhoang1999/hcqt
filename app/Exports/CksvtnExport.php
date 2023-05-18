<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CksvtnExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_svtn_cvl')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->khoi_nganh,
                $ts->ssvtn,
                $ts->xuat_sac,
                $ts->gioi,
                $ts->kha ,
                $ts->ty_le,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Khối ngành",
            "Số sinh viên tốt nghiệp",
            "Loại xuất sắc (Phân loại tốt nghiệp (%) (ĐT))",
            "Loại giỏi (Phân loại tốt nghiệp (%) (ĐT))",
            "Loại khá (Phân loại tốt nghiệp (%) (ĐT))",
            "Tỷ lệ sinh viên tốt nghiệp có việc làm sau 1 năm ra trường (%)",
            
        ];
    }
}
