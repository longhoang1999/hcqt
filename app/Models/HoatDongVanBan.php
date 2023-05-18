<?php

namespace App\Models;

use App\Models\LinhVucVanBan;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HoatDongVanBan extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'maso',
        'ten',
        'tenngan',
        'linhvuc_id',
        'mota'
     ];
     protected $casts = [
     ];
     
    protected $table = 'hoatdongvanbans';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function linhvuc() {
        return $this->hasOne(LinhVucVanBan::class, 'id', 'linhvuc_id');
    }

}