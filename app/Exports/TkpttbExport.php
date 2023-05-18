<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class TkpttbExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_tk_phong_tb')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->tp_gd_lap,
                $ts->so_luong,
                $ts->dien_tich_xay,
                $ts->doi_tuong_sd,
                $ts->trang_thiet_bi ,
                $ts->so_huu,
                $ts->lien_ket,
                $ts->thue,
                $ts->so_phong_PKC,
                $ts->dien_tich_PKC,
                $ts->so_phong_PBKC,
                $ts->dien_tich_PBKC,
                $ts->so_phong_PT,
                $ts->dien_tich_PT,
                $ts->loai_phong,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Tên phòng/Giảng đường/Lab",
            "Số lượng",
            "Diện tích sàn xây dựng (m2)",
            "Đối tượng sử dụng",
            "Danh mục trang thiết bị chính",
            "Sở hữu (Hình thức sử dụng)",
            "Liên kết (Hình thức sử dụng)",
            "Thuê (Hình thức sử dụng)",
            "Số phòng (Phòng kiên cố)",
            "Diện tích (m2) (Phòng kiên cố)",
            "Số phòng (Phòng bán kiên cố)",
            "Diện tích (m2) (Phòng bán kiên cố)",
            "Số phòng (Phòng tạm)",
            "Diện tích (m2) (Phòng tạm)",
            "Loại Phòng (I: Phòng là việc, II: Phòng học/ Giảng đường/ Lab)",
            
        ];
    }
}
