<?php

namespace App\Models;

use App\Common\Constant\LichConstants;
use App\Common\Constant\UploadFileConstants;
use App\Models\DonVi;
use App\Models\LichGiangVien;
use App\Models\Room;
use App\Models\User;
use App\Services\LichService;


use Illuminate\Database\Eloquent\Model;

class Lich extends Model
{
    use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'tieude', 'noidung', 'nguoitao_id', 'nguoichutri_id', 'donvitao_id', 'nguoithamgia_ids',
         'donvi_ids', 'thoigian_tu', 'thoigian_den','donvitinhthoigian','trangthai',
         'nguoidieuchinh_id', 'nguoixacnhan_id', 'loai_lich', 'luuy','source_chu_tri','source_chu_tri_id'
    ];
    protected $table = 'lichs';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

   
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'nguoitao_id' => 'integer',
        'nguoichutri_id' => 'integer',
        'donvitinhthoigian' => 'integer',
        'trangthai' => 'integer',
        'nguoidieuchinh_id' => 'integer',
        'nguoixacnhan_id' => 'integer',
        'loailich' => 'integer',
        'nguoithamgia_ids' => 'array',
         'donvi_ids' => 'array',
    ];

    protected $appends = ['room_ids', 'comment', 'lich_giang_vien'];
    
     // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
         return $date->format('c');
    }
    
    public function nguoitao() {
        return $this->hasOne(User::class, 'id', 'nguoitao_id');
    }
    public function nguoichutri() {
        return $this->hasOne(User::class, 'id', 'nguoichutri_id');
    }
    public function groupchutri (){
        return $this->hasOne(Group::class, 'id', 'source_chu_tri_id');
    }
    public function donvitao() {
        return $this->hasOne(DonVi::class, 'id', 'donvitao_id');
    }
    public function nguoidieuchinh() {
        return $this->hasOne(User::class, 'id', 'nguoidieuchinh_id');
    }
    public function nguoixacnhan() {
        return $this->hasOne(User::class, 'id', 'nguoixacnhan');
    }
    public function diadiem(){
        return $this->belongsToMany(Room::class, 'room_booking', 'lich_id', 'room_id')
        ->withPivot('outsite_booking_room_id', 'room_id');
    }
    public function danhsach_nguoithamgia()
    {
        return $this->belongsToMany(User::class, 'lich_nguoithamgia', 'lich_id', 'user_id' )
        ->withPivot('daidien_donvi_id', 'status', 'lich_id', 'user_id', 'accepted_at');
    }
    public function danhsach_donvithamgia()
    {
        return $this->belongsToMany(DonVi::class, 'lich_nguoithamgia', 'lich_id', 'daidien_donvi_id')
        ->withPivot('daidien_donvi_id','user_id', 'status', 'lich_id', 'accepted_at');
    }
    public function danhsach_groupthamgia()
    {
        return $this->belongsToMany(Group::class, 'lich_nguoithamgia', 'lich_id', 'group_id')
        ->withPivot('group_id','user_id', 'status', 'lich_id', 'accepted_at');
    }
    public function lichduyet()
    {
        return $this->hasMany('\App\Models\LichDuyet', 'lich_id', 'id');
        // ->withPivot('daidien_donvi_id', 'status');
    }
    public function comments()
    {
        return $this->hasMany('\App\Models\LichComment', 'lich_id', 'id');
    }
    /* 
    public function isLichNhaTruong() {
        return $this->loai_lich == LichConstants::LOAI_LICH_NHA_TRUONG;
    }
    public function isLichLanhDao() {
        return $this->loai_lich == LichConstants::LOAI_LICH_LANH_DAO;
    }
    public function isLichDonVi() {
        return $this->loai_lich == LichConstants::LOAI_LICH_DON_VI;
    }
    public function isLichCaNhan() {
        return $this->loai_lich == LichConstants::LOAI_LICH_CA_NHAN;
    }

    public function isTrungLichChuTri() {
        //TODO check
        return false;
    } */
    public function isTrungDiaDiem() {
        //TODO check

        return false;
    }
    
    /**
     * Trùng với lịch của user
     */
    public function hasLichTrung() {
        $lst_lichtrung = $this->DanhSach_LichTrung();
        return isset($lst_lichtrung) && count($lst_lichtrung) > 0;
    }
    public function DanhSach_LichTrung() {
        
        // lấy danh sách lịch của user
        $lst_lich_trung = LichService::getAllLichTrungByUserIdAndTime(
            auth()->user()->id,
            $this->id,
            $this->thoigian_tu,
            $this->thoigian_den);
        return $lst_lich_trung;
    }

    /**
     * trùng lịch người chủ trì
     */
    public function hasLichTrung_ChuTri() {
        $lst_lichtrung = $this->DanhSach_LichTrung_ChuTri();
        return isset($lst_lichtrung) && count($lst_lichtrung) > 0;
    }
    public function DanhSach_LichTrung_ChuTri() {
        
        // lấy danh sách lịch của user
        $lst_lich_trung = LichService::getAllLichTrungByUserIdAndTime(
            $this->nguoichutri_id,
            $this->id,
            $this->thoigian_tu,
            $this->thoigian_den);
        return $lst_lich_trung;
    }
    public function getRoomIdsAttribute () {
    
        $lst_diadiem = $this->diadiem->map(function ($diadiem) {
            return $diadiem->id;
        });
        return $lst_diadiem;
    }
    public function getCommentAttribute () {
        try {
            $lst_comment = $this->comments->filter(function ($comment) {
                return $comment->status == $this->trangthai;
            });
            if (count($lst_comment) == 0) {
                return "";
            }
            $lst_comment = $lst_comment->sortByDesc('created_at');
            //$lst_comment = collect($lst_comment->values()->all());
            $comment = $lst_comment[0];
            
            if (isset($comment) && $comment != null) {
                return $comment->comment;
            }
        }catch(\Exception $e) {
            error_log($e->getMessage());
        }
        
        return "";
    }
     
    public function attachedFiles(){
        return $this->hasMany('\App\Models\UploadFile', 'fk_id', 'id')
            ->where('source', UploadFileConstants::LICH)
            ->where('category', UploadFileConstants::CATEGORY_DOCUMENT);
        
    }
    //bien ban buoi hop
    public function bienBanFiles(){
        return $this->hasMany('\App\Models\UploadFile', 'fk_id', 'id')
            ->where('source', UploadFileConstants::LICH)
            ->where('category', UploadFileConstants::CATEGORY_BIEN_BAN);
        
    }
    
    public function getLichGiangVienAttribute() {
        if ($this->loai_lich == LichConstants::LOAI_LICH_GIANG_DAY
            || $this->loai_lich == LichConstants::LOAI_LICH_COI_THI
            || $this->loai_lich == LichConstants::LOAI_LICH_CHAM_THI) {
                return LichGiangVien::where('lich_id', $this->id)->first();
            }
    }
    
}
