<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ThongBaoComment extends Model
{
    use \App\Traits\EditorsTrait;
    // use SoftDeletes;
    
    protected $fillable = [
        'thongbao_id',
        'nguoitao_id',
        'noi_dung'
     ];
     protected $casts = [
        
     ];
     
    protected $table = 'thongbao_comment';
    //protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function nguoitao(){
        return $this->hasOne('App\Models\User', 'id', 'nguoitao_id');
    }
    // public function donvithamgia(){
    //     return $this->hasOne('App\Models\DonVi', 'ma_donvi','daidien_donvi_id');
    // }
    public function lich(){
        return $this->hasOne('App\Models\thongbao', 'id', 'thongbao_id');
    }
   

}