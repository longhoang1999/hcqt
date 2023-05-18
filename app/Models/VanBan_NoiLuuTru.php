<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_NoiLuuTru extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'donvi_id',
        'noiluutru',
        'trangthai',
        'tinhtrang',
        'ngay_luu',
        'ngay_kiemtra_cuoi',
        'ngay_huyluu',
        'nguoi_luu_id',
        'nguoi_kiemtra_cuoi_id',
        'nguoi_huyluu_id',
        'ghichu'
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'donvi_id' => 'integer',
        'trangthai' => 'integer',
        'tinhtrang' => 'integer',
        'nguoi_luu_id' => 'integer',
        'nguoi_kiemtra_cuoi_id' => 'integer',
        'nguoi_huyluu_id' => 'integer',
     ];
     
    protected $table = 'vanban_noiluutru';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan', 'vanban_id');
    }
    
    public function DonViLuuTru(){
        return $this->hasOne('App\Models\DonVi', 'donvi_id');
    }
    public function NguoiLuuTru(){
        return $this->hasOne('App\Models\User', 'nguoi_luu_id');
    }
    public function NguoiKiemTraCuoi(){
        return $this->hasOne('App\Models\User', 'nguoi_kiemtra_cuoi_id');
    }
    public function NguoiHuyLuuTru(){
        return $this->hasOne('App\Models\User', 'nguoi_huyluu_id');
    }

}