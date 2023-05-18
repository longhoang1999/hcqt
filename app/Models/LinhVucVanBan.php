<?php

namespace App\Models;

use App\Models\DonVi;
//use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\HoatDongVanBan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LinhVucVanBan extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'maso',
        'ten',
        'tenngan',
        'donviphutrach_id',
        'mota'
     ];
     protected $casts = [
     ];
     
    protected $table = 'linhvucvanbans';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function donviphutrach() {
        return $this->hasOne(DonVi::class, 'id', 'donviphutrach_id');
    }
    public function hoatdongvanban() {
        return $this->hasMany(HoatDongVanBan::class, 'linhvuc_id', 'id');
    }

}