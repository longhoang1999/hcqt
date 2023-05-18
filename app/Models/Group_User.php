<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Group_User extends Model
{
    //use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'group_id', 'user_id', 'chuc_vu_id', 'ma_chuc_vu'
    ];
    protected $table = 'group_user';
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
    
    public function group() {
        return $this->hasOne(\App\Models\Group::class,'id','group_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
