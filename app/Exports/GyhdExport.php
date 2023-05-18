<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use DB;


class GyhdExport implements FromView
{
    public function view(): View
    {
        $getGyhd = [];
        $gyhds = DB::table('huongdan AS hd')
                ->select("hd.id", "hd.mo_ta as mo_ta_gyhd", "hd.loai_tieuchuan", "hd.bo_tieuchuan_id", "btc.tieu_de as tieu_de_btc")
                ->leftjoin('bo_tieuchuan AS btc', 'btc.id', '=', 'hd.bo_tieuchuan_id')
                ->where("hd.trang_thai", "<>",  "deleted")
                ->orderBy("hd.id", "asc")
                ->get();

        foreach($gyhds as $gyhd){
            // tiêu chí áp dụng
            $roles = DB::table("role_gyhd_tchi")->where("gyhd_id", $gyhd->id)
                ->where("bo_tieuchuan_id", $gyhd->bo_tieuchuan_id)
                ->select("tieuchi_id")
                ->get();
            $spanUI = "";
            foreach($roles as $role){
                $tchi_tc = DB::table("tieuchi as tchi")
                            ->select(
                                "tchi.stt as stt_tchi", 
                                "tchi.id", 
                                "tchi.mo_ta", 
                                "tchi.tieuchuan_id",
                                "tc.stt as stt_tc", 
                            )
                            ->leftjoin('tieuchuan AS tc', 'tc.id', '=', 'tchi.tieuchuan_id')
                            ->where("tchi.id", $role->tieuchi_id)
                            ->first();

                $spanUI .= $tchi_tc->stt_tc .".". $tchi_tc->stt_tchi ."<br>";
            }

            $row = (object) array(
                'mo_ta'        =>   $gyhd->mo_ta_gyhd,
                'tieu_de_btc'  =>   $gyhd->tieu_de_btc,
                'spanUI'       =>   $spanUI
            );
            array_push($getGyhd, $row);
        }

        return view('admin.project.ViewExportExcelSystem.export-gyhd', [
            'getGyhd' => $getGyhd
        ]);
    }

    
}
