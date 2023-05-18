<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class VanBan_XinYKien extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'vanban_id',
        'donvi_id',
        'user_id',
        'is_require',
        'received_at',
        'date_start_plan',
        'date_end_plan',
        'date_start_act',
        'date_end_act',
        'ghichu'
     ];

     protected $casts = [
        'vanban_id' => 'integer',
        'donvi_id' => 'integer',
        'user_id' => 'integer',
     ];
     
    protected $table = 'vanban_xinykien';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function DonViDuocXinYKien(){
        return $this->belongsTo('App\Models\DonVi', 'donvi_id');
    }
    public function NguoiDuocXinYKien(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function NguoiTao(){
        return $this->belongsTo('App\Models\User', 'created_by');
    }
    public function VanBan(){
        return $this->belongsTo('App\Models\VanBan', 'vanban_id');
    }
    
    
   

}