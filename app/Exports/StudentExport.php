<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class StudentExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getStudent = [];
        $svs = DB::table('excel_import_dlsinhvien')->get();
        foreach($svs as $sv){
            
            $row = [
                $sv->masv ,
                $sv->ho,
                $sv->ten,
                $sv->ntns,
                $sv->gioitinh,
                $sv->email,
                $sv->phone,
                $sv->cccd,
                $sv->lop,
                $sv->xa,
                $sv->huyen,
                $sv->tinh,
                $sv->dantoc,
                $sv->quoctich,
                $sv->tennganh,
                $sv->manganh,
                $sv->manganhTS,
                $sv->kqxhtnam1,
                $sv->kqxhtnam3,
                $sv->kqxhtnam4,
                $sv->kqxhtnam5,
                $sv->nbdck,
                $sv->nktkh,
                $sv->trangthai,
                $sv->namnh,
                $sv->namtn,
                $sv->namqd,
                $sv->namnb,
                $sv->baocaobo,
                $sv->trinhdo,
                $sv->namdulien,

            ];
            array_push($getStudent, $row);
        }
        return collect($getStudent);
    }

    public function headings() :array {
        return [
            "Mã SV",
            "Họ",
            "Tên",
            "Ngày tháng năm sinh",
            "Giới tính",
            "Email",
            "Điện thoại",
            "Số CCCD/Hộ chiếu",
            "Lớp",
            "Xã/Phường thường trú",
            "Quận/Huyện thường trú",
            "Tỉnh/thành phố thường trú",
            "Dân tộc",
            "Quốc tịch",
            "Tên ngành",
            "Mã ngành",
            "Mã ngành TS",
            "KQ Xét HT năm 1",
            "KQ Xét HT năm 2",
            "KQ Xét HT năm 3",
            "KQ Xét HT năm 4",
            "KQ Xét HT năm 5",
            "Năm bắt đầu của khóa",
            "Năm kết thúc khóa học",
            "Trạng thái",
            "Năm nhập học",
            "Năm tốt nghiệp",
            "Năm QĐ",
            "Năm nhận bằng",
            "Báo cáo Bộ",
            "Trình độ",
            "Năm dữ liệu",

        ];
    }
}
