<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class LichNguoiThamGia extends Model
{
    use \App\Traits\EditorsTrait;
    // use SoftDeletes;
    
    protected $fillable = [
        'lich_id',
        'user_id',
        'daidien_donvi_id',
        'group_id',
        'status',
        'accepted_at'
     ];
     protected $casts = [
        
     ];
     
    protected $table = 'lich_nguoithamgia';
    //protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function nguoithamgia(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    // public function donvithamgia(){
    //     return $this->hasOne('App\Models\DonVi', 'ma_donvi','daidien_donvi_id');
    // }
    public function lich(){
        return $this->hasOne('App\Models\Lich', 'id', 'lich_id');
    }
   

}