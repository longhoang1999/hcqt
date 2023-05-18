<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class ListKhhdExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public function collection()
    {
        $getCTDT = [];
        $csdts = $this->dataExceptDelete(
                DB::table("kehoach_hanhdong")->orderBy('kehoach_hanhdong.tieu_de', 'asc')
                    ->leftjoin('hoatdongnhom', 'hoatdongnhom.id', '=', 'kehoach_hanhdong.hoatdongnhom_id')
                    ->get()
        );
        
        foreach($csdts as $csdt){

            $donvi = DB::table("donvi")->where("id", $csdt->donvi_id)
                        ->select('ten_donvi')->first();
            $khccsl = DB::table("kehoach_cc_solieu")->where("id", $csdt->kehoach_id)
                        ->select('dv_kiemtra')->first();
            $ns_kiemtra = DB::table("users")->where("id", $khccsl->dv_kiemtra)
                    ->select('name','donvi_id')->first();
            $dv_kiemtra = DB::table("donvi")->where("id", $ns_kiemtra->donvi_id)
                    ->select("ten_donvi")->first();
            $row = [

                $csdt->noi_dung,
                $csdt->ngay_batdau,
                $csdt->ngay_hoanthanh,
                $donvi->ten_donvi,
                $ns_kiemtra->name . ' - ' . $dv_kiemtra->ten_donvi,
                $csdt->ghi_chu,
            ];
            array_push($getCTDT, $row);
        }
        return collect($getCTDT);
    }

    public function headings() :array {
        return [
            "Nội dung",
            "Ngày thực hiện",
            "Ngày kiểm tra",
            "Đơn vị thực hiện",
            "Người kiểm tra",
            "Ghi chú",
        ];
    }
}
