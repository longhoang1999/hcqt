<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkgttlExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_giaotrinh')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->tgt_tltk ,
                $ts->nxb,
                $ts->ke_hoach,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Tên giáo trình, tài liệu tham khảo (kể cả giáo trình điện tử)",
            "Năm xuất bản",
            "Kế hoạch soạn thảo giáo trình, tài liệu tham khảo (kể cả giáo trình điện tử)",
            
        ];
    }
}
