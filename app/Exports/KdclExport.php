<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class KdclExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_kdcl')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->doi_tuong ,
                $ts->btcdg   ,
                $ts->nhtbcttdg1,
                $ts->ncnbctdg,
                $ts->ten_tcdg ,
                $ts->thang_nam_dgn,
                $ts->kqdgchd,
                $ts->gcn_ngay_cap,
                $ts->gcn_gia_tri_den,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Đối tượng",
            "Bộ tiêu chuẩn đánh giá",
            "Năm hoàn thành báo cáo TĐG lần 1 ",
            "Năm cập nhật báo cáo TĐG",
            "Tên tổ chức đánh giá",
            "Tháng/năm đánh giá ngoài",
            "Kết quả đánh giá của Hội đồng KĐCLGD (TDVCN)",
            "Ngày cấp(Giấy chứng nhận)",
            "Giá trị đến(Giấy chứng nhận)",
            
        ];
    }
}
