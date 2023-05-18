<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use Lang;

class UnitExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getUnit = [];
        $donvis = DB::table('donvi')->where("trang_thai", "<>",  'deleted')->get();
        foreach($donvis as $dv){
            // Loại đơn vị
            $ldv = DB::table("loai_donvi")->where("id", $dv->loai_dv_id)->select("loai_donvi")
                    ->first()->loai_donvi;
            $truong_dv = DB::table("users")->where("id", $dv->truong_dv)->select("name");
            if($truong_dv->count() > 0){
                $truong_dv = $truong_dv->first()->name;
            }else{
                $truong_dv = Lang::get('project/Standard/title.kctt');
            }
            // đơn vị cấp cha
            $textDv = "";
            $dvcc = $this->dequi($textDv, $dv);

            $row = [
                $dv->ma_donvi,
                $dv->ten_donvi,
                $ldv,
                $dvcc,
                $truong_dv
            ];
            array_push($getUnit, $row);
        }
        return collect($getUnit);
    }

    public function headings() :array {
        return [
            Lang::get('project/Standard/title.madv'),
            Lang::get('project/Standard/title.tendvi'),
            Lang::get('project/Standard/title.loaidv'),
            Lang::get('project/Standard/title.dvcc'),
            Lang::get('project/Standard/title.truongdvi'),
        ];
    }

    public function dequi($textDv, $donvi) {
        if($donvi->dvcc != null){
            $dvPa = DB::table("donvi")->where("id", $donvi->dvcc)
                    ->select("ten_donvi", "id", "dvcc")->first();
            if($textDv != "")
                $textDv = $dvPa->ten_donvi  . " - " . $textDv;
            else $textDv = $dvPa->ten_donvi;
            return $this->dequi($textDv, $dvPa);
        }else{
            return $textDv;
        }
    }
}
