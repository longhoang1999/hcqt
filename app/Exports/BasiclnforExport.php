<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class BasiclnforExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getBasiclnfor = [];
        $dvs = DB::table('excel_import_donvi')->get();
        foreach($dvs as $dv){
            //loại đơn vị
            $ldv = DB::table('loai_donvi')->where("id", $dv->loai_dv_id)->select('loai_donvi')
                    ->first();
            $row = [
                $dv->ma_donvi ,
                $ldv->loai_donvi,
                $dv->ten_donvi_TV,
                $dv->ten_donvi_TA,
                $dv->viet_tat_TV,
                $dv->viet_tat_TA,
                $dv->ten_truoc_day,
                $dv->chu_quan,
                date("d/m/Y", strtotime($dv->ngay_thanh_lap)),
                $dv->soqd,
                $dv->lv_hoat_dong,
                $dv->diachi,
                $dv->phone,
                $dv->fax,
                $dv->email,
                $dv->website,
                $dv->ghichu,
                $dv->loai_hinh,
                $dv->tgdtk1,
                $dv->tgcbk1,

            ];
            array_push($getBasiclnfor, $row);
        }
        return collect($getBasiclnfor);
    }

    public function headings() :array {
        return [
            "Mã đơn vị",
            "Loại đơn vị",
            "Tên đơn vị (*)(Tiếng Việt)",
            "Tên đơn vị (*)(Tiếng Anh)",
            "Tên viết tắt (*)(Tiếng Việt)",
            "Tên viết tắt (*)(Tiếng Anh)",
            "Tên trước đây",
            "Cơ quan/Bộ chủ quản",
            "Ngày thành lập",
            "Số quyết định",
            "Lĩnh vực hoạt động",
            "Địa chỉ",
            "Số điện thoại liên hệ",
            "Số fax",
            "Email",
            "Website",
            "Ghi chú",
            "Loại hình cơ sở giáo dục",
            "Thời gian bắt đầu đào tạo khóa I",
            "Thời gian cấp bằng tốt nghiệp cho khoá I",
        ];
    }
}
