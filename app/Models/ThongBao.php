<?php

namespace App\Models;

use App\Common\Constant\UploadFileConstants;
use App\Models\ThongBao_User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ThongBao extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'subject',
        'keyword',
        'status',
        'body',
        'creator_id',
        'publicer_id',
        'public_at',
        'linhvuc_ids',
        'receiver_ids',
        'donvi_receiver_ids',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
     ];
     protected $casts = [
        'status' => 'integer',
        'creator_id' => 'integer',
        'publicer_id' => 'integer',
        'receiver_ids' => 'array',
        'linhvuc_ids' => 'array',
     ];
     
    protected $table = 'thongbaos';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function receivers(){
        return $this->belongsToMany('\App\Models\User', 'thongbao_user', 'thongbao_id', 'user_id')
        ->withPivot('star_flg', 'read_at', 'from_folder');
    }
    public function creator(){
        return $this->hasOne('\App\Models\User', 'id', 'creator_id');
    }
    public function attachedFiles(){
        return $this->hasMany('\App\Models\UploadFile', 'fk_id', 'id')->where('category', UploadFileConstants::THONG_BAO);
    }
    public function linhvuc()
    {
        return $this->belongsToMany(LinhVucVanBan::class,'thongbao_linhvuc','thongbao_id','linhvuc_id');
    }
}