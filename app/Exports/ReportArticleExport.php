<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class ReportArticleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getbbbc = [];
        $bbbcs = DB::table('excel_import_baibao_baocao')->get();
        foreach($bbbcs as $bbbc){
            
            $row = [
                $bbbc->tbbbc ,
                $bbbc->maso,
                $bbbc->linhvuc,
                $bbbc->tacgia,
                $bbbc->donvipk,
                $bbbc->tcd,
                $bbbc->so_issn_isbn,
                $bbbc->sodang,
                $bbbc->namdang,
                $bbbc->loai,
                $bbbc->ltc,
                $bbbc->dmtc,
                $bbbc->url,

            ];
            array_push($getbbbc, $row);
        }
        return collect($getbbbc);
    }

    public function headings() :array {
        return [
            "Tên bài báo/báo cáo",
            "Mã số",
            "Lĩnh vực",
            "Tác giả",
            "Đơn vị (Phòng/Khoa/TT)",
            "Tạp chí đăng",
            "Số ISSN/ISBN",
            "Số đăng",
            "Năm đăng",
            "Loại",
            "Loại tạp chí",
            "Danh mục tạp chí",
            "Đường dẫn/file đính kèm
            (nếu có)",
    
        ];
    }
}
