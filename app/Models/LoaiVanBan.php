<?php

namespace App\Models;

use App\Models\NhomVanBan;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class LoaiVanBan extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'maso',
        'ten',
        'tenngan',
        'nhomvanban_id',
        'mota'
     ];
     protected $casts = [
     ];
     
    protected $table = 'loaivanbans';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function NhomVanBan() {
        return $this->hasOne(NhomVanBan::class, 'id', 'nhomvanban_id');
    }

}