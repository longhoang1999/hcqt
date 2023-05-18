<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class DsbctdgExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public function collection()
    {
        $getCTDT = [];
        $csdts = $this->dataExceptDelete(
                DB::table('kehoach_baocao')->get()
        );
        
        foreach($csdts as $csdt){
            $userName = DB::table("users")
                        ->where("id", $csdt->ns_phutrach)->select("name")->first();

            if($csdt->loai_tieuchuan == 'csdt'){
                $csodt = 'CCơ sở đào tạo';
            }else{
                $csodt = 'Chương trình đào tạo';
            }

            $row = [

                $csdt->ten_bc,
                $csodt,
                $csdt->trang_thai,
                $csdt->ngay_batdau,
                $csdt->ngay_hoanthanh,
                $userName->name,
            ];
            array_push($getCTDT, $row);
        }
        return collect($getCTDT);
    }

    public function headings() :array {
        return [
            "Tên báo cáo",
            "Loại báo cáo",
            "Trạng thái",
            "Ngày bắt đầu",
            "Ngày kết thúc",
            "Cán bộ chuyên trách",
        ];
    }
}
