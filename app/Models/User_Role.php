<?php

namespace App\Models;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class User_Role extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'role_id', 'donvi_id'
    ];
    protected $table = 'user_role';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;


    protected $hidden = [
        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'role_id' => 'integer',
        'donvi_id' => 'integer',
    ];
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
     public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
    public function role()
    {
        return $this->hasOne(Role::class);
    } 

}
