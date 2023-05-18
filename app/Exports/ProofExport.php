<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class ProofExport extends DefaultExport implements FromCollection, WithHeadings 
{
    public function collection()
    {
        $getCTDT = [];
        $csdts = $this->dataExceptDelete(
                DB::table('minhchung')
                ->leftJoin('users','users.id','=','minhchung.nguoi_tao')
                ->leftJoin('donvi','users.donvi_id','=','donvi.id')
                ->select('minhchung.id as mc_id','tieu_de','ngay_ban_hanh','noi_banhanh','cong_khai','count_size','ten_donvi', 'trich_yeu','minhchung.deleted_at', 'minhchung.sohieu', 'minhchung.tinh_trang')
                ->orderBy('minhchung.updated_at','desc')->get()
        );
        
        foreach($csdts as $csdt){
            if($csdt->cong_khai == 'Y'){
                $minhchung = 'Công Khai';
            }else{
                $minhchung = 'Không công khai';
            }

            if($csdt->tinh_trang == "xacnhan"){
                $tt = "Xác nhận";
            }else if($csdt->tinh_trang == "dangcho"){
                $tt = "Đang chờ";
            }else{
                $tt = "Không xác nhận";
            }

            $row = [
                $csdt->tieu_de,
                $csdt->sohieu,
                $csdt->ngay_ban_hanh,
                $csdt->noi_banhanh,
                $csdt->ten_donvi,
                $minhchung,
                $tt
            ];
            array_push($getCTDT, $row);
        }
        return collect($getCTDT);
    }

    public function headings() :array {
        return [
            "Tiêu đề minh chứng",
            "Số hiệu",
            "Ngày ban hành",
            "Nơi ban hành",
            "ĐV quản lý",
            "Loại minh chứng",
            "Tình trạng",
        ];
    }
}
