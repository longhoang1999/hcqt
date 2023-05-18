<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CompilationBookExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getBssach = [];
        $bsss = DB::table('excel_import_biensoansach')->get();
        foreach($bsss as $bss){
            
            $row = [
                $bss->donvi,
                $bss->masach,
                $bss->tensach,
                $bss->loaisach,
                $bss->chubien,
                $bss->thanhvien,
                $bss->namdk,
                $bss->namnt,
                $bss->namxb,
                $bss->nhaxb,
                $bss->hpsd,
                $bss->nhsd,
                $bss->trangthai,

            ];
            array_push($getBssach, $row);
        }
        return collect($getBssach);
    }

    public function headings() :array {
        return [
            "Đơn vị",
            "Mã sách",
            "Tên sách",
            "Loại sách",
            "Chủ biên",
            "Thành viên",
            "Năm đăng ký",
            "Năm nghiệm thu",
            "Năm xuất bản",
            "Nhà xuất bản",
            "Học phần sử dụng",
            "Ngành học sử dụng",
            "Trạng thái",
    
        ];
    }
}
