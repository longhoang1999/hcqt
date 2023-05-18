<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class InventExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getsangche = [];
        $scs = DB::table('excel_import_sangche')->get();
        foreach($scs as $sc){
            
            $row = [
                $sc->tpmsc ,
                $sc->maso,
                $sc->loai,
                $sc->tacgia,
                $sc->cshcn,
                $sc->cshdv,
                $sc->linhvuc,
                $sc->scn,
                $sc->namcap,
                $sc->noicap,

            ];
            array_push($getsangche, $row);
        }
        return collect($getsangche);
    }

    public function headings() :array {
        return [
            "Tên phát minh/sáng chế",
            "Mã số",
            "Loại",
            "Tác giả",
            "Chủ sở hữu cá nhân",
            "Chủ sở hữu đơn vị",
            "Lĩnh vực",
            "Số công nhận",
            "Năm cấp",
            "Nơi cấp",
    
        ];
    }
}
