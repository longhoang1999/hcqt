<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use App\Exports\DefaultExport;

class ListUserExport extends DefaultExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $getUsers = [];
        $users = $this->dataExceptDelete(
            DB::table('users')->get()
        );
        foreach($users as $user){
            // đơn vị
            $donvi = DB::table("donvi")->where("id", $user->donvi_id)
                                ->select("ten_donvi")->first();
            if($donvi)
                $tenDV =  $donvi->ten_donvi;
            else $tenDV =  "";

            // chức vụ
            $chucvu = DB::table("role_chucvu_users")
                        ->where("user_id", $user->id)
                        ->select("chucvu_id")->first();
            if($chucvu)
                $ten_chuc_vu  =  DB::table("chuc_vu")->where("id", $chucvu->chucvu_id)
                    ->select("ten_chuc_vu")->first()->ten_chuc_vu;
            else $ten_chuc_vu  = "";

            // người tạo
            $createHuman = DB::table('users')->where('id', $user->nguoi_tao)
                        ->select('name')->first();
            if($createHuman){
                $nameHuman  =  $createHuman->name;
            }else $nameHuman  = "";
            
            $row = [
                $user->ma_nhansu,
                $user->email,
                $user->name,
                $tenDV,
                $ten_chuc_vu,
                date("d/m/Y", strtotime($user->created_at)),
                $nameHuman,
            ];
            array_push($getUsers, $row);
        }
        return collect($getUsers);
    }

    public function headings() :array {
        return [
            "Mã nhân sự",
            "Tên đăng nhập",
            "Tên nhân sự",
            "Đơn vị",
            "Chức vụ",
            "Ngày tạo",
            "Người tạo"
        ];
    }
}
