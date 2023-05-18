<?php

namespace App\Models;

use App\Models\DonVi_User;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonVi extends Model
{
    // use SoftDeletes;
    use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'ten_donvi', 'ten_ngan', 'ma_donvi', 'dia_chi', 'mo_ta', 'truong_dv',
         'canbo_dbcl', 'nguoi_tao','csdt_id','trang_thai', 'group_chat_lock_at'
    ];
    protected $table = 'donvi';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

   
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'truong_dv' => 'integer',
        'canbo_dbcl' => 'integer',
        'nguoi_tao' => 'integer',
        'csdt_id' => 'integer',
    ];

     // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function csdt() {
        return $this->belongsTo(\App\Models\Csdt::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class,'donvi_user','donvi_id','user_id')
        ->withPivot('chucvu_cd');
    }
    public function lichs(){
        return $this->belongsToMany(Lich::class, 'lich_nguoithamgia', 'daidien_donvi_id', 'lich_id');
    }
}
