<?php

namespace App\Models;

use App\Common\Constant\UploadFileConstants;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class VanBan_ButPhe_GiaoViec extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'butphe_id',
        'vanban_id',
        'nguoi_giaoviec_id',
        'donvi_xuly_id',
        'nguoi_xuly_id',
        'ngay_giaoviec',
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
        'ghichu',
     ];

     protected $casts = [
        'butphe_id' => 'integer',
        'vanban_id' => 'integer',
        'nguoi_giaoviec_id' => 'integer',
        'donvi_xuly_id' => 'integer',
        'nguoi_xuly_id' => 'integer',
        'nguoi_xacnhan_id' => 'integer',
        'tiendo_xuly' => 'integer',
        'trangthai' => 'integer',
        'result_point' => 'integer',
     ];
     
    protected $table = 'vanban_butphe_giaoviec';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function VanBanButPhe(){
        return $this->hasOne('App\Models\VanBan_ButPhe','id', 'butphe_id');
    }
    
    public function NguoiGiaoViec(){
        return $this->hasOne('App\Models\User','id', 'nguoi_giaoviec_id');
    }
    
    public function NguoiXuLy(){
        return $this->hasOne('App\Models\User','id', 'nguoi_xuly_id');
    }
    
    public function DonViXuLy(){
        return $this->hasOne('App\Models\DonVi','id', 'donvi_xuly_id');
    }
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan','id', 'vanban_id');
    }
    
    public function fileKetQua(){
        return $this->hasMany('\App\Models\UploadFile', 'fk_id', 'id')
            ->where('source', UploadFileConstants::SOURCE_VAN_BAN)
            ->where('category', UploadFileConstants::CATEGORY_VAN_BAN_DEN_KET_QUA_THUC_HIEN);
    }

}