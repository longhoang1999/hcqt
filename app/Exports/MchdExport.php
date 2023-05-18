<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class MchdExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public function collection()
    {
        $getCTDT = [];
        $csdts = $this->dataExceptDelete(
                DB::table("hoatdongnhom")->orderBy('created_at', 'desc')->get()
        );
        
        foreach($csdts as $csdt){
            $lvuc = DB::table("nhom_mc_sl")->where("id", $csdt->nhom_mc_sl_id)->first();
            $mcyc = DB::table("hoatdongnhom")->where("parent", $csdt->id)->count();
            $row = [

                $csdt->year,
                $lvuc->mo_ta,
                $csdt->noi_dung,
                $mcyc,
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
            "MC yêu cầu",
        ];
    }
}
