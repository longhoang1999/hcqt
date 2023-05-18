<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkdsgvknExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_gvtkn')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->hoten,
                $ts->namsinh,
                $ts->gioitinh == "1" ? "Nam" : "Nữ",
                $ts->chucdanh,
                $ts->tddt,
                $ts->cngd,
                $ts->khoinganh,
            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Tên",
            "Năm sinh",
            "Giới tính",
            "Chức danh",
            "Trình độ đào tạo",
            "Chuyên ngành giảng dạy",
            "Khối ngành"
        ];
    }
}
