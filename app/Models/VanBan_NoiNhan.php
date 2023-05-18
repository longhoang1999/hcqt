<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_NoiNhan extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'donvi_id',
        'user_id',
        'is_internal',
        'received_at',
        'ngay_tralai',
        'ngay_thuhoi',
        'nguoi_nhan_id',
        'nguoi_tralai_id',
        'nguoi_thuhoi_id',
        'ngay_thaythe_id',
        'ghichu'
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'donvi_id' => 'integer',
        'user_id' => 'integer',
        'is_internal' => 'boolean',
        'nguoi_nhan_id' => 'integer',
        'nguoi_tralai_id' => 'integer',
        'nguoi_thuhoi_id' => 'integer',
        'ngay_thaythe_id' => 'integer'
     ];
     
    protected $table = 'vanban_noinhan';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan', 'vanban_id');
    }
    
    public function DonViNhanVanBan(){
        return $this->hasOne('App\Models\DonVi','id', 'donvi_id');
    }
    public function NguoiNhanVanBan(){
        return $this->hasOne('App\Models\User','id', 'user_id');
    }
    public function NguoiNhan(){
        return $this->hasOne('App\Models\User', 'nguoi_nhan_id');
    }
    public function NguoiTraLai(){
        return $this->hasOne('App\Models\User', 'nguoi_tralai_id');
    }
    public function NguoiThuHoi(){
        return $this->hasOne('App\Models\User', 'nguoi_thuhoi_id');
    }
    public function NguoiThayThe(){
        return $this->hasOne('App\Models\User', 'nguoi_thaythe_id');
    }

}