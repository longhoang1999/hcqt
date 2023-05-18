<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class ListStandardExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public $idStandards;
    public function __construct($id) {
        $this->idStandards = $id;
    }
    public function collection()
    {
        $getStandard = [];
        $tcs = $this->dataExceptDelete(
            DB::table('tieuchuan')->where("bo_tieuchuan_id", $this->idStandards)->get()
        );

        foreach($tcs as $tc){
            $createHuman = DB::table('users')->where('id', $tc->nguoi_tao)
                                    ->select('first_name', 'last_name')->first();
            if($createHuman){
                $nameHuman = $createHuman->first_name . ' ' . $createHuman->last_name;
            }else{
                $nameHuman = "";
            }
            $row = [
                date("d/m/Y", strtotime($tc->created_at)),
                $nameHuman,
                $tc->mo_ta,
            ];
            array_push($getStandard, $row);
        }
        return collect($getStandard);
    }

    public function headings() :array {
        return [
            "Ngày tạo",
            "Người tạo",
            "Tên tiêu chuẩn",
        ];
    }
}
