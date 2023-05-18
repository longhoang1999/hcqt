<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_ButPhe extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'donvi_xuly_id',
        'nguoi_xuly_id',
        'is_main',
        'received_at',
        'nguoi_nhan_id',
        'execute_date_start_plan',
        'execute_date_end_plan',
        'execute_date_start_act',
        'execute_date_end_act',
        'nguoi_xacnhan_id',
        'ngay_xacnhan',
        'tiendo_xuly',
        'trangthai',
        'result_point',
        'ghichu'
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'donvi_xuly_id' => 'integer',
        'nguoi_xuly_id' => 'integer',
        'nguoi_nhan_id' => 'integer',
        'nguoi_xacnhan_id' => 'integer',
        'tiendo_xuly' => 'integer',
        'trangthai' => 'integer',
        'result_point' => 'integer',
     ];
     
    protected $table = 'vanban_butphe';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function NguoiButPhe(){
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
    public function DonViXuLy(){
        return $this->hasOne('App\Models\DonVi','id', 'donvi_xuly_id');
    }
    public function NguoiXyLy(){
        return $this->hasOne('App\Models\User', 'id', 'nguoi_xuly_id');
    }
    
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan', 'id', 'vanban_id');
    }
   
    public function DsNguoiGiaoViec(){
        //TODO can test cau lenh nay
        return $this->belongsToMany('\App\Models\User', 'vanban_butphe_giaoviec', 'vanban_id', 'nguoi_giaoviec_id')
        ->withPivot('butphe_id', 'vanban_id', 'nguoi_giaoviec_id', 'donvi_xuly_id', 'nguoi_xuly_id',
        'ngay_giaoviec', 'received_at', 'execute_date_start_plan', 'execute_date_end_plan', 'execute_date_start_act',
        'execute_date_end_act', 'nguoi_xacnhan_id', 'ngay_xacnhan', 'tiendo_xuly', 'trangthai', 'result_point',
        'ghichu');
    }
    
    public function DsNguoiDuocGiaoViec(){
        //TODO can test cau lenh nay
        return $this->belongsToMany('\App\Models\User', 'vanban_butphe_giaoviec', 'vanban_id', 'nguoi_xuly_id')
        ->withPivot('butphe_id', 'vanban_id', 'nguoi_giaoviec_id', 'donvi_xuly_id', 'nguoi_xuly_id',
        'ngay_giaoviec', 'received_at', 'execute_date_start_plan', 'execute_date_end_plan', 'execute_date_start_act',
        'execute_date_end_act', 'nguoi_xacnhan_id', 'ngay_xacnhan', 'tiendo_xuly', 'trangthai', 'result_point',
        'ghichu');
    }

}