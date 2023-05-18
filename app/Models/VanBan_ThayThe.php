<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_ThayThe extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'vanban_thaythe_id',
        'loai_thaythe',
        'ngay_nhan',
        'nguoi_nhan',
        'ngay_hieuluc',
        'ngay_xacnhan_thuchien',
        'nguoi_xacnhan_thuchien_id',
        'ghichu',
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'nguoi_nhan_id' => 'integer',
        'vanban_thaythe_id' => 'integer',
        'loai_thaythe' => 'integer',
        'nguoi_xacnhan_thuchien_id',
     ];
     
    protected $table = 'vanban_thaythe';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan', 'vanban_id');
    }
    
    public function VanBanThayThe(){
        return $this->hasOne('App\Models\VanBan', 'vanban_thaythe_id');
    }
    public function NguoiXacNhanThucHien(){
        return $this->hasOne('App\Models\User', 'nguoi_xacnhan_thuchien_id');
    }
    
}