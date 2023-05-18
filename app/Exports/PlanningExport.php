<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class PlanningExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public function collection()
    {
        $getCTDT = [];
        $ctdts = $this->dataExceptDelete(
                DB::table('kehoach_cc_solieu AS ccsl')
                	->leftjoin('nhom_mc_sl AS mcsl', 'mcsl.id', '=', 'ccsl.nhom_mc_sl_id')->get()
        );
        
        foreach($ctdts as $ctdt){
            // người tạo
       		$dvThucHien = DB::table("donvi")->where("id", $ctdt->dv_thuchien)
                                ->select("ten_donvi","ten_ngan","ma_donvi","dia_chi")
                                ->first();
            if($dvThucHien){
            	$dvpt = $dvThucHien->ten_donvi;
            }else{
            	$dvpt = "";
            }

            $nsKiemTra =  DB::table("users AS us")
                                ->where("us.id", $ctdt->dv_kiemtra)
                                ->select("us.name", "dv.ten_donvi")
                                ->leftjoin('donvi AS dv', 'dv.id', '=', 'us.donvi_id')
                               	->first();
            if($nsKiemTra){
            	$nskt = $nsKiemTra->name." - ".$nsKiemTra->ten_donvi;  
            }else{
            	$nskt = "";
            }

            $row = [
                $ctdt->mo_ta,
                $ctdt->ngay_batdau,
                $ctdt->ngay_hoanthanh,
                $dvpt,
                $nskt,
                $dvThucHien->ten_ngan,
                $dvThucHien->ma_donvi,
                $dvThucHien->dia_chi,
            ];
            array_push($getCTDT, $row);
        }
        return collect($getCTDT);
    }

    public function headings() :array {
        return [
            "Lĩnh Vực",
            "Ngày bắt đầu",
            "Ngày hoàn thành",
            "Đơn vị phụ trách",
            "Nhân sự kiểm tra",
            "Tên ngắn",
            "Mã đơn vị",
            "Địa chỉ",
        ];
    }
}
