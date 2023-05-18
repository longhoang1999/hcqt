<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class AwardExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getgiaithuong = [];
        $gts = DB::table('excel_import_giaithuong')->get();
        foreach($gts as $gt){
            
            $row = [
                $gt->tgt ,
                $gt->ckt,
                $gt->linhvuc,
                $gt->nam,
                $gt->doituong,
                $gt->ndc,
                $gt->dvc,

            ];
            array_push($getgiaithuong, $row);
        }
        return collect($getgiaithuong);
    }

    public function headings() :array {
        return [
            "Tên giải thưởng",
            "Cấp khen thưởng",
            "Lĩnh vực",
            "Năm",
            "Đối tượng",
            "Người được cấp",
            "Đơn vị cấp",
    
        ];
    }
}
