<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class Tktcexport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getAdmissions = [];
        $tss = DB::table('excel_import_tk_tai_chinh')->get();
        
        foreach($tss as $ts){
            $row = [
                $ts->noi_dung ,
                $ts->n_2019,
                $ts->n_2020,
                $ts->n_2021,
                $ts->n_2022,
                $ts->n_2023,

            ];
            array_push($getAdmissions, $row);
        }
        return collect($getAdmissions);
    }

    public function headings() :array {
        return [
            "Nội dung",
            "2019",
            "2020",
            "2021",
            "2022",
            "2023",
            
        ];
    }
}
