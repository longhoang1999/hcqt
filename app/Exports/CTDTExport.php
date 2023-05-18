<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class CTDTExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public function collection()
    {
        $getCTDT = [];
        $ctdts = $this->dataExceptDelete(
                DB::table('ctdt')->orderBy('created_at', 'desc')->get()
        );
        
        foreach($ctdts as $ctdt){
            // người tạo
            $createHuman = DB::table('users')->where('id', $ctdt->nguoi_tao)
                                    ->select('name')->first();
            if($createHuman){
                $nameHuman = $createHuman->name;
            }else{
                $nameHuman = "";
            }
            // hệ đào tạo
            $hdt = DB::table('he_dao_tao')->where("id", $ctdt->hedaotao_id)
                    ->select("ten_hdt")->first();
            if($hdt){
                $nameHdt = $hdt->ten_hdt;
            }else{
                $nameHdt = "";
            }
            // đơn vị
            $donvi = DB::table('donvi')->where("id", $ctdt->donvi_id)
                    ->select("ten_donvi")->first();
            if($donvi){
                $nameDonvi = $donvi->ten_donvi;
            }else{
                $nameDonvi = "";
            }

            $row = [
                $ctdt->ma_ctdt,
                $ctdt->tennganh,
                $ctdt->tennganh_en,
                $nameHdt,
                $nameDonvi,
                date("d/m/Y", strtotime($ctdt->created_at)),
                $nameHuman,
            ];
            array_push($getCTDT, $row);
        }
        return collect($getCTDT);
    }

    public function headings() :array {
        return [
            "Mã CTĐT",
            "Tên CTĐT Tiếng Việt",
            "Tên CTĐT Tiếng Anh",
            "Hệ đào tạo",
            "Đơn vị",
            "Ngày tạo",
            "Người tạo"
        ];
    }
}
