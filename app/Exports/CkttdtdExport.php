<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkttdtdExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_dtd_dtxxd')->get();
        
        foreach($tss as $ts){
            if($ts->parent == "")
                $row1 = $ts->stt;
            else
                $row1 = $ts->parent . '.' . $ts->stt;
            $row = [
                $row1,
                $ts->noi_dung,
                $ts->dien_tich,
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
            "Thứ tự",
            "Nội dung",
            "Diện tích (m2)",
            "Sở hữu (Hình thức sử dụng)",
            "Liên kết (Hình thức sử dụng)",
            "Thuê (Hình thức sử dụng)",            
        ];
    }
}
