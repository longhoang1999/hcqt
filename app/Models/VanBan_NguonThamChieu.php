<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_NguonThamChieu extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'vanban_thamchieu_id',
        'nguon_thamchieu',
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'vanban_thamchieu_id' => 'integer',
     ];
     
    protected $table = 'vanban_nguonthamchieu';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function VanBan(){
        return $this->hasOne('App\Models\VanBan', 'vanban_id');
    }
    
    public function VanBanThamChieu(){
        return $this->hasOne('App\Models\VanBan', 'vanban_thamchieu_id');
    }

}