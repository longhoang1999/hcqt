<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CtdtExport_ implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('export_import_ctdt')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->khoa_BM ,
                $ts->cndt,
                $ts->ma_nganh,
                $ts->ten_ctdt,
                $ts->lhdt,
                $ts->nam_bddt,
                $ts->diadiem_tochuc,
                $ts->slsv,
                $ts->sl_svtn,
                $ts->ten_vanbang,
                $ts->tddt,
                $ts->tgdtc,
                $ts->cttshn,
                $ts->mkndt,
                $ts->mlvdt,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Khoa/ Bộ môn",
            "Ngành/ Chuyên ngành ĐT",
            "Mã ngành",
            "Tên CTĐT",
            "Loại hình đào tạo",
            "Năm bắt đầu đào tạo",
            "Địa điểm tổ chức đào tạo",
            "Số lượng sinh viên hiện tại",
            "Số SVTN",
            "Tên văn bằng",
            "Trình độ đào tạo",
            "Thời gian đào tạo chuẩn",
            "Chỉ tiêu tuyển sinh hàng năm",
            "Mã khối ngành đào tạo",
            "Mã lĩnh vực đào tạo",
        ];
    }
}
