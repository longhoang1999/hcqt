<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SoPhatHanhVanBan extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'csdt_id',
        'maso',
        'loaivanban_id',
        'namphathanh',
        'so_kyhieu',
        'donvi_id',
        'nguoi_xin_id',
        'ngay_xin',
        'vanban_sudung_id',
        'ngay_sudung',
        'nguoi_sudung_id',
        'ngay_cap',
        'nguoi_cap_id',
     ];
     protected $casts = [
        'csdt_id' => 'integer',
        'loaivanban_id' => 'integer',
        'namphathanh' => 'integer',
        'donvi_id' => 'integer',
        'nguoi_xin_id' => 'integer',
        'nguoi_sudung_id' => 'integer',
        'nguoi_cap_id' => 'integer',
        'vanban_sudung_id' => 'integer',
     ];
     
    protected $table = 'sophathanhvanbans';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;
    

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function loaivanban(){
        return $this->hasOne('\App\Models\LoaiVanBan', 'id', 'loaivanban_id');
    }
    public function csdt(){
        return $this->hasOne('\App\Models\Csdt', 'id', 'csdt_id');
    }
    
    
    // public function getDocTypeNameAttribute () {
    //     return $this->vb_type == 1 ? 'Văn bản đến' : ($this->vb_type == 2 ? 'Văn bản phát hành' : ($this->vb_type == 0 ? 'Dự thảo' : ''));
    // }
}