<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use DB;


class MinimunExport implements FromView
{
    public function view(): View
    {
        $getMinimum = [];
        $mctts = DB::table('minhchung_tt AS mctt')
                ->select("mctt.id", "mctt.tieu_de as tieu_de_mctt", "mctt.loai_tieuchuan", "mctt.bo_tieuchuan_id", "btc.tieu_de as tieu_de_btc")
                ->leftjoin('bo_tieuchuan AS btc', 'btc.id', '=', 'mctt.bo_tieuchuan_id')
                ->where("mctt.deleted_at", null)
                ->orderBy("mctt.id", "asc")
                ->get();

        foreach($mctts as $mctt){
            // tiêu chí áp dụng
            $roles = DB::table("role_mctt_tchi")->where("mctt_id", $mctt->id)
                ->where("bo_tieuchuan_id", $mctt->bo_tieuchuan_id)
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
                'tieu_de_mctt' =>   $mctt->tieu_de_mctt,
                'tieu_de_btc'  =>   $mctt->tieu_de_btc,
                'spanUI'       =>   $spanUI
            );
            array_push($getMinimum, $row);
        }

        return view('admin.project.ViewExportExcelSystem.export-minimun', [
            'getMinimum' => $getMinimum
        ]);
    }

    
}
