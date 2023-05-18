<?php

namespace App\Models;

use App\Models\DonVi;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NhomVanBan extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'maso',
        'ten',
        'tenngan',
        'mota'
     ];
     protected $casts = [
     ];
     
    protected $table = 'nhomvanbans';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function LoaiVanBan(){
        return $this->hasMany('\App\Models\LoaiVanBan', 'nhomvanban_id','id');
    }

}