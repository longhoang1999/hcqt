<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class exceltactionExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public function collection()
    {
        $getCTDT = [];
        $ctdts = $this->dataExceptDelete(
                DB::table('hoatdongnhom AS hdn')
                    ->leftjoin("nhom_mc_sl AS mcsl" , 'mcsl.id', '=', 'hdn.nhom_mc_sl_id')
                    ->select("hdn.id AS id_hdn", "hdn.year", "hdn.nhom_mc_sl_id", "mcsl.mo_ta",
                                "hdn.noi_dung", "hdn.parent", 'mcsl.id',"hdn.deleted_at")
                    ->orderBy('hdn.year', 'desc')
                    ->where("parent", 0)
                    ->get()
        );
        
        foreach($ctdts as $ctdt){
            $mcycs = DB::table("hoatdongnhom")->where("parent", $ctdt->id_hdn);
            $dsMCYC = "";
            $dsNgbd = "";
            $dsNght = "";
            $dsDv = '';
            foreach($mcycs->get() as $mcyc){
                $dsMCYC = $dsMCYC == "" ? $dsMCYC . $mcyc->noi_dung : $dsMCYC . "|" . $mcyc->noi_dung;
                $dsNgbd = $dsNgbd == "" ? $dsNgbd . date("d/m/Y", strtotime($mcyc->ngay_batdau)) : $dsNgbd . "|" . date("d/m/Y", strtotime($mcyc->ngay_batdau));
                $dsNght = $dsNght == "" ? $dsNght . date("d/m/Y", strtotime($mcyc->ngay_hoanthanh)) : $dsNght . "|" . date("d/m/Y", strtotime($mcyc->ngay_hoanthanh));

                $hdn_dv = DB::table("hoatdongnhom_donvi AS hdnDv")
                        ->leftjoin("donvi AS dv" , 'dv.id', '=', 'hdnDv.donvi_id')
                        ->where("hdnDv.hoatdongnhom_id", $mcyc->id)
                        ->select("hdnDv.donvi_id", "dv.ten_donvi")->get();
                foreach($hdn_dv as $hdndv){
                    $dsDv = $dsDv == "" ? $dsDv . $hdndv->ten_donvi : $dsDv . "|" . $hdndv->ten_donvi;
                }
            }

        

            $row = [
                $ctdt->year,
                $ctdt->mo_ta,
                $ctdt->noi_dung,
                $mcycs->count(),
                $dsMCYC,
                $dsDv,
                $dsNgbd,
                $dsNght
            ];
            array_push($getCTDT, $row);
        }
        return collect($getCTDT);
    }

    public function headings() :array {
        return [
            "Năm",
            "Lĩnh vực",
            "Hoạt động",
            "Số Minh chứng yêu cầu",
            "Tên minh chứng yêu cầu",
            "Đơn vị thự hiện",
            "Ngày bắt đầu",
            "Ngày hoàn thành",
        ];
    }
}
