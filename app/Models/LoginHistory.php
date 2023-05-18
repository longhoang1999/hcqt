<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoginHistory extends Model
{
    
    protected $fillable = [
        'id',
        'access_history_id',
        'user_id',
        'account',
        'last_login_at',
        'last_logout_at',
        'login_failure_times'
     ];

     protected $casts = [
         'id' => 'string',
        'access_history_id' => 'string',
        'login_failure_times' => 'integer',
        'user_id' => 'integer'
     ];
     
    protected $table = 'loginhistories';
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

    public function AccessHistory(){
        return $this->hasOne('App\Models\AccessHistory', 'id', 'access_history_id');
    }
    

}