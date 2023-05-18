<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class StandardExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getStandard = [];
        $btcs = DB::table('bo_tieuchuan')->where("deleted_at", null)->get();
        foreach($btcs as $btc){
            // Loại tiêu chuẩn
            $ldg = $btc->loai_tieuchuan == "csgd" ? "Cơ sở giáo dục" : "Chương trình đào tạo";
            // người tạo
            $createHuman = DB::table('users')->where('id', $btc->nguoi_tao)
                                    ->select('name')->first();
            if($createHuman){
                $nameHuman = $createHuman->name;
            }else{
                $nameHuman = "";
            }
            $row = [
                $btc->tieu_de,
                $ldg,
                date("d/m/Y", strtotime($btc->created_at)),
                $nameHuman,
                $btc->trang_thai
            ];
            array_push($getStandard, $row);
        }
        return collect($getStandard);
    }

    public function headings() :array {
        return [
            "Tên tiêu chuẩn",
            "Loại đánh giá",
            "Ngày tạo",
            "Người tạo",
            "Trạng thái"
        ];
    }
}
