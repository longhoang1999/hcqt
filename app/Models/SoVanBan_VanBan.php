<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SoVanBan_VanBan extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $fillable = [
        'id',
        'sovanban_id',
        'vanban_id',
        'trangthai'
     ];
     protected $casts = [
        'sovanban_id' => 'integer',
        'vanban_id' => 'integer',
        'trangthai' => 'integer',
     ];
     
    protected $table = 'sovanban_vanban';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;
    

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
}