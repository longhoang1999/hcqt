<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CkcsgdExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_csgd_ctgd')->get();
        
        foreach($tss as $ts){
            $time = $this->convertTime($ts->ngay_cap);
            $time_1 = $this->convertTime($ts->gia_tri_den);
            $time_2 = $this->convertTime($ts->tddgn);
            $row = [
                $ts->ten_co_so,
                $time_2,
                $ts->ket_qua,
                $ts->nghi_quyet,
                $ts->cong_nhan, 
                $time,
                $time_1,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }
    public function convertTime($dateNot){
        $arr = explode('-', $dateNot);
        $dateConvert = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
        return $dateConvert;

    } 
    public function headings() :array {
        return [
            "Tên cơ sở đào tạo hoặc các chương trình đào tạo",
            "Thời điểm đánh giá ngoài",
            "Kết quả đánh giá/Công nhận",
            "Nghị quyết của Hội đồng KĐCLGD",
            "Công nhận đạt/không đạt chất lượng giáo dục",
            "Ngày cấp",
            "Giá trị đến",
            
        ];
    }
}
