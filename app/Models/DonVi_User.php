<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DonVi_User extends Model
{
    //use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'donvi_id', 'user_id', 'chucvu_cd', "role_ids", "grouprole_ids"
    ];
    protected $table = 'donvi_user';
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
        'donvi_id' => 'integer',
        'user_id' => 'integer',
    ];

     // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function donvi() {
        return $this->hasOne(\App\Models\DonVi::class,'id','donvi_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
    public function chucvu()
    {
        return $this->hasOne(ChucVu::class);
    }

    public static function buildDonviUser($id) {
        $donvi_user = DonVi_User::find($id);
        if (isset($donvi_user)) {
            $donvi_user->load('chucvu');
            $donvi_user->grouproles = GroupRole::wherein('id', $donvi_user->grouprole_ids)
            ->get()->load('roles');

            $donvi_user->roles = Role::find($donvi_user->role_ids);
        }
        return $donvi_user;
    }
}
