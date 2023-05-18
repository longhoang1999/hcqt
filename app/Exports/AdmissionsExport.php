<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class AdmissionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_tuyensinh')->get();
        
        foreach($tss as $ts){
            $loai = "";
            if($ts->loai == "1")
                $loai = "Nghiên cứu sinh";
            elseif($ts->loai == "2")
                $loai = "Học viên cao học";
            elseif($ts->loai == "3")
                $loai = "Đại học";
            elseif($ts->loai == "4")
                $loai = "Cao đẳng";
            elseif($ts->loai == "5")
                $loai = "Trung cấp";
            elseif($ts->loai == "6")
                $loai = "Khác";
            $row = [
                $ts->dt_tg ,
                $loai,
                $ts->ctdt,
                $ts->stsdt	,
                $ts->s_t_t,
                $ts->tlct,
                $ts->snhtt,
                $ts->dtdv,
                $ts->dtbcnhdt,
                $ts->slsvqtnh,
                $ts->hdt,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Đối tượng, thời gian (năm)",
            "Loại",
            "Chương trình đào tạo",
            "Số thí sinh dự tuyển (người)",
            "Số trúng tuyển (người)",
            "Tỷ lệ cạnh tranh",
            "Số nhập học thực tế (người)",
            "Điểm tuyển đầu vào (thang điểm 30)",
            "Điểm trung bình của người học được tuyển",
            "Số lượng sinh viên quốc tế nhập học (người)",
            "Hệ đào tạo",
            
        ];
    }
}
