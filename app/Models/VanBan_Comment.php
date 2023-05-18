<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_Comment extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'donvi_comment_id',
        'nguoi_comment_id',
        'ngay_comment',
        'phan_loai_comment',
        'for_comment_id',
        'noi_dung'
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'donvi_comment_id' => 'integer',
        'nguoi_comment_id' => 'integer',
        'phan_loai_comment' => 'integer',
        'for_comment_id' => 'integer',
     ];
     
    protected $table = 'vanban_comment';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan', 'vanban_id');
    }
        
    public function NguoiComment(){
        return $this->hasOne('App\Models\User', 'nguoi_comment_id');
    }
    public function DonViComment(){
        return $this->hasOne('App\Models\DonVi', 'donvi_comment_id');
    }

}