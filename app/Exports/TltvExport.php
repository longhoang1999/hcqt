<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class TltvExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_tailieu_thuvien')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->ma_hoc_phan ,
                $ts->ten_hoc_phan,
                $ts->khoi_nganh,
                $ts->syctdc_sck,
                $ts->sach_in_sck,
                $ts->sach_dt_sck,
                $ts->syctdc_sgt,
                $ts->sach_in_sgt,
                $ts->sach_dt_sgt,
                $ts->ttnn ,
                $ts->tnndn,
                $ts->syctdchp_stk,
                $ts->sach_in_stk,
                $ts->sach_dt_stk,
                $ts->syctdc_shd,
                $ts->sach_in_shd,
                $ts->sach_dt_shd,
                $ts->sldtcbi,
                $ts->sldtcdt,
                $ts->ttnn_tltk ,
                $ts->tnndn_tltk,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Mã học phần",
            "Tên học phần",
            "Khối ngành",
            "Số yêu cầu trong đề cương HP (Sách chuyên khảo)",
            "Sách in (Sách chuyên khảo)",
            "Sách điện tử (Sách chuyên khảo)",
            "Số yêu cầu trong đề cương HP (Sách giáo trình)",
            "Sách in (Sách giáo trình)",
            "Sách điện tử (Sách giáo trình)",
            "Trên 5 năm (Năm xuất bản GTC)",
            "Từ 5 năm đến nay (Năm xuất bản GTC)",
            "Số yêu cầu trong đề cương HP (Sách tham khảo)",
            "Sách in (Sách tham khảo)",
            "Sách điện tử (Sách tham khảo)",
            "Số yêu cầu trong đề cương HP (Sách hướng dẫn)",
            "Sách in (Sách hướng dẫn)",
            "Sách điện tử (Sách hướng dẫn)",
            "Số lượng đầu tạp chí bản in (Tạp chí do trường xuất bản)",
            "Số lượng đầu tạp chí điện tử (Tạp chí do trường xuất bản)",
            "Trên 5 năm (Năm xuất bản TLTK)",
            "Từ 5 năm đến nay (Năm xuất bản TLTK)",
        ];
    }
}
