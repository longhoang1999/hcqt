<?php

namespace App\Models;
use App\Common\Constant\UploadFileConstants;
use App\Models\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'phanloai',
        'tenvanban',
        'trich_yeu',
        'sothutu',
        'sophathanhvanban_id',
        'sokyhieuvanban',
        'loaivanban_id',
        'coquanphathanhvanban_id',
        'domatvanban_id',
        'dokhanvanban_id',
        'ngayden',
        'ngayphathanh',
        'ngay_ket_thuc_xin_y_kien_plan',
        'ngay_ket_thuc_xin_y_kien_act',
        'ngay_ket_thuc_kiem_tra_the_thuc_plan',
        'ngay_ket_thuc_kiem_tra_the_thuc_act',
        'nguoi_kiem_tra_the_thuc_id',
        'ngay_trinh_ky',
        'nguoi_trinh_ky_id',
        'ngaycohieuluc',
        'ngayhethieuluc',
        'soban',
        'soto',
        'so_van_ban_id',
        'donvi_phathanh_id',
        'nguoi_phathanh_id',
        'minhchung',
        'trangthai',
        'nguoi_pheduyet_id',
        'ngay_pheduyet',
        'nguoi_xacnhan_hoanthanh_id',
        'ngay_xacnhan_hoanthanh',
        'phan_loai_xu_ly',
        'nguoi_nhanpheduyet_id',
        'ngay_nhanpheduyet',
        'ngay_trinh_chuyen',
        'nguoi_trinh_chuyen_id',
        'noinhan_noibo_ids',
        'noinhan_benngoai_ids'
     ];
     protected $casts = [
        'phanloai'              => 'integer',
        'sothutu'               => 'integer',
        'sophathanhvanban_id'   => 'integer',
        'loaivanban_id'         => 'integer',
        'coquanphathanhvanban_id' => 'integer',
        'domatvanban_id'        => 'integer',
        'dokhanvanban_id'       => 'integer',
        'soban'                 => 'integer',
        'soto'                  => 'integer',
        'nguoi_kiem_tra_the_thuc_id' => 'integer',
        'nguoi_trinh_ky_id' => 'integer',
        'donvi_phathanh_id'     => 'integer',
        'nguoi_phathanh_id'     => 'integer',
        'trangthai'             => 'integer',
        'minhchung'             => 'boolean',
        'nguoi_pheduyet_id' => 'integer',
        'nguoi_xacnhan_hoanthanh_id' => 'integer',
        'phan_loai_xu_ly' => 'integer',
        'nguoi_nhanpheduyet_id' => 'integer',
     ];
     
    protected $table = 'vanban';
    protected $primaryKey = 'id'; // or null
    protected $keyType = 'biginteger';
    public $timestamps = true;
    public $incrementing = true;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }


    public function NguoiDangKy(){
        return $this->belongsTo('\App\Models\User', 'created_by');
    }
    public function NguoiPheDuyet(){
        return $this->belongsTo('\App\Models\User', 'nguoi_pheduyet_id');
    }
    public function NguoiKiemTraThucThe(){
        return $this->belongsTo('\App\Models\User', 'nguoi_kiem_tra_the_thuc_id');
    }
    public function NguoiTrinhKy(){
        return $this->belongsTo('\App\Models\User', 'nguoi_trinh_ky_id');
    }
    public function NguoiPhatHanh(){
        return $this->belongsTo('\App\Models\User', 'nguoi_phathanh_id');
    }
    public function NguoiXacNhanHoanThanh(){
        return $this->belongsTo('\App\Models\User', 'nguoi_xacnhan_hoanthanh_id');
    }
    public function NguoiTrinhChuyen(){
        return $this->belongsTo('\App\Models\User', 'nguoi_trinh_chuyen_id');
    }
    
    public function CoQuanPhatHanhVanBan(){
        return $this->belongsTo('\App\Models\CoQuanPhatHanhVanBan', 'coquanphathanhvanban_id');
    }
    
    public function SoVanBan(){
        return $this->belongsTo('\App\Models\SoVanBan', 'so_van_ban_id');
    }
    public function SoPhatHanh(){
        return $this->belongsTo('\App\Models\SoPhatHanhVanBan', 'sophathanhvanban_id');
    }
    public function LoaiVanBan(){
        return $this->belongsTo('\App\Models\LoaiVanBan', 'loaivanban_id');
    }
    public function DoKhanVanBan(){
        return $this->belongsTo('\App\Models\DoKhanVanBan', 'dokhanvanban_id');
    }
    
    public function DoMatVanBan(){
        return $this->belongsTo('\App\Models\DoMatVanBan', 'domatvanban_id');
    }
    
    public function attachedFiles(){
        return $this->hasMany('\App\Models\UploadFile', 'fk_id', 'id')
        ->where('source', UploadFileConstants::SOURCE_VAN_BAN)
        ->where('category', UploadFileConstants::CATEGORY_VAN_BAN);
    }
    
    //but phe - giao viec
    public function NguoiButPhe(){
        return $this->hasOne('\App\Models\User', 'nguoi_pheduyet_id');
    }
    public function DsNguoiDuocGiaoViec(){
    //TODO can test
        return $this->belongsToMany('\App\Models\User', 'vanban_butphe_giaoviec', 'vanban_id', 'nguoi_xuly_id')
        ->withPivot('butphe_id', 'vanban_id', 'nguoi_giaoviec_id', 'donvi_xuly_id', 'nguoi_xuly_id',
        'ngay_giaoviec', 'received_at', 'execute_date_start_plan', 'execute_date_end_plan', 'execute_date_start_act',
        'execute_date_end_act', 'nguoi_xacnhan_id', 'ngay_xacnhan', 'tiendo_xuly', 'trangthai', 'result_point',
        'ghichu');
    
    }
    public function DsButPhe(){
        return $this->hasMany('\App\Models\VanBan_ButPhe', 'vanban_id');
    }
    public function DsGiaoViec(){
        return $this->hasMany('\App\Models\vanban_butphe_giaoviec', 'vanban_id')->orderBy('donvi_xuly_id');
    }
    public function DsDonViButPhe(){
        return $this->belongsToMany('\App\Models\DonVi', 'vanban_butphe', 'vanban_id', 'donvi_xuly_id')
        ->withPivot('vanban_id', 'donvi_xuly_id', 'nguoi_xuly_id', 'is_main',
        'ngay_butphe', 'received_at', 'execute_date_start_plan', 'execute_date_end_plan', 'execute_date_start_act',
        'execute_date_end_act', 'nguoi_xacnhan_id', 'ngay_xacnhan', 'tiendo_xuly', 'trangthai', 'result_point',
        'ghichu');
    }
    public function DsUserButPhe(){
        return $this->belongsToMany('\App\Models\User', 'vanban_butphe', 'vanban_id', 'nguoi_xuly_id')
        ->withPivot('vanban_id', 'donvi_xuly_id', 'nguoi_xuly_id', 'is_main',
        'ngay_butphe', 'received_at', 'execute_date_start_plan', 'execute_date_end_plan', 'execute_date_start_act',
        'execute_date_end_act', 'nguoi_xacnhan_id', 'ngay_xacnhan', 'tiendo_xuly', 'trangthai', 'result_point',
        'ghichu');
    }
    
    //Comment
    public function DsComment(){
        return $this->hasMany('\App\Models\VanBan_Comment', 'vanban_id')
        ->orderBy('created_at', 'desc');
    }
    
    //van ban thay the
    public function DsVanBanThayThe() {
        return $this->belongsToMany('\App\Models\VanBan', 'vanban_thaythe', 'vanban_id', 'vanban_thaythe_id')
        ->withPivot('vanban_id','vanban_thaythe_id','loai_thaythe','ngay_nhan','nguoi_nhan','ngay_hieuluc','ngay_xacnhan_thuchien','nguoi_xacnhan_thuchien_id','ghichu',);
    }
    
    //van ban Noi luu tru
    public function DsNoiLuuTru() {
        return $this->hasMany('\App\Models\VanBan_NoiLuuTru', 'vanban_id');
    }
    
    //van ban Noi nhan
    public function DsNoiNhan() {
        return $this->hasMany('\App\Models\VanBan_NoiNhan', 'vanban_id');
    }
    
    //van ban Nguon tham chieu
    public function DsNguonThamChieu() {
        return $this->hasMany('\App\Models\VanBan_NguonThamChieu', 'vanban_id');
    }
    
    //hoat dong
    public function DsHoatDong(){
        return $this->belongsToMany('\App\Models\HoatDongVanBan','vanban_hoatdong','vanban_id','hoatdong_id');
    }
    
    //xin y kien
    public function DsXinYKien(){
        return $this->hasMany('\App\Models\VanBan_XinYKien', 'vanban_id');
    }
    public function DsSoVanBan() {
        return $this->belongsToMany('\App\Models\SoVanBan', 'sovanban_vanban', 'vanban_id', 'sovanban_id')
        ->withPivot('created_at', 'created_by', 'updated_at', 'updated_by', 'vanban_id', 'sovanban_id', 'trangthai');
    }
    
    public function DonViPhatHanh() {
        return $this->belongsTo('\App\Models\DonVi', 'donvi_phathanh_id');
    }
    public function NhomNguoiDung(){
        return $this->hasOne(Group::class,'vanban_id','id');
    }
    //bo tu day --- > end
    
        
    
    // public function DsButPhe(){
    //     //return $this->belongsToMany('\App\Models\VanBanDenButPhe', 'vanbanden_id', 'id');
    //     return App\Models\VanBanDenButPhe::where('vanbanden_id', $this->id)->get();
        
    // }
    /* public function getDsDonVi(){
        return $this->belongsToMany(DonVi::class,'vanbanden_noinhanxuly','vanbanden_id','donvi_id');
    }
    public function getDsGui(){
        return $this->hasMany('App\Models\VanBanDen_NoiNhanXuLy','vanbanden_id','id');
    } */
    // public function getDsThuHoi(){
    //     return $this->hasMany('App\Models\VanBanDen_NoiNhanXuLy','vanbanden_id','id')->where('thuhoi', 1);
    // }
}