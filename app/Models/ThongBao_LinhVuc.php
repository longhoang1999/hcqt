<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ThongBao_LinhVuc extends Model
{
    //use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'thongbao_id', 'linhvuc_id'
    ];
    protected $table = 'thongbao_linhvuc';
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
        'group_id' => 'integer',
        'user_id' => 'integer',
    ];

     // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function thongbao() {
        return $this->hasOne(\App\Models\ThongBao::class,'id','thongbao_id');
    }
    public function linhvuc()
    {
        return $this->hasOne(LinhVucVanBan::class,'id','linhvuc_id');
    }
}
