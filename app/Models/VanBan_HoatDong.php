<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_HoatDong extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'hoatdong_id',
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'hoatdong_id' => 'integer',
     ];
     
    protected $table = 'vanban_hoatdong';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan', 'vanban_id');
    }
    
    public function VanBanHoatDong(){
        return $this->hasOne('App\Models\HoatDong', 'hoatdong_id');
    }

}