<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;


class ManafieldExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getManafield = [];
        // $linhvucs = $this->dataExceptDelete(
        //         DB::table('nhom_mc_sl')->orderBy('created_at', 'desc')->get()
        //     );

        $linhvucs = DB::table('nhom_mc_sl')
                    ->where("deleted_at", null)
                    ->orderBy('created_at', 'desc')->get();
        foreach($linhvucs as $linhvuc){
            $createHuman = DB::table('users')->where('id', $linhvuc->nguoi_tao)
                                    ->select('name')->first();
            if($createHuman){
                $nameHuman = $createHuman->name;
            }else{
                $nameHuman = "";
            }
            $donvi_pt = DB::table('donvi')
                            ->select('ten_donvi')
                            ->where('id',$linhvuc->donvi_id)
                            ->first();
            if($donvi_pt){
                $nameDonvi = $donvi_pt->ten_donvi;
            }else{
                $nameDonvi = "";
            }
            $row = [
                $linhvuc->mo_ta,
                $nameDonvi,
                date("d/m/Y", strtotime($linhvuc->created_at)),
                $nameHuman,
            ];
            array_push($getManafield, $row);
        }
        return collect($getManafield);
    }

    public function headings() :array {
        return [
            "Nội dung lĩnh vực",
            "Đơn vị phụ trách",
            "Ngày tạo",
            "Người tạo",
        ];
    }
}
