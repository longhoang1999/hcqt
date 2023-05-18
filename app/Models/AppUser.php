<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\AppUser_Prekey;
use App\Models\AppUser_SignedPreKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class AppUser extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'registrationId',
        'deviceId',
        'userId',
        'identityKey',
        'prKey'

     ];
     protected $casts = [
        'id' => 'string',
        'registrationId' => 'integer',
        'deviceId' => 'integer',
        'userId' => 'integer',
     ];
     
    protected $table = 'appusers';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
    
    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function userInf() {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
    public function preKey() {
        return $this->hasOne(AppUser_Prekey::class, 'appuser_id', 'id');
    }
    public function signedPreKey() {
        return $this->hasOne(AppUser_SignedPreKey::class, 'appuser_id', 'id');
    }
    public function pKey() {
        return $this->hasOne(AppUser_PK::class, 'appuser_id', 'id');
    }

}