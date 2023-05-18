<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ChucVu extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'chucvu_cd',
        'ten',
        'mota'
     ];
     protected $casts = [
     ];
     
    protected $table = 'chucvus';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    /* public function users()
    {
        return $this->belongsToMany(User::class,'donvi_user','chucvu_cd','user_id')
        ->withPivot('donvi_id');
    } */

}