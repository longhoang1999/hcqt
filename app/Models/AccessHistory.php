<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccessHistory extends Model
{
    
    protected $fillable = [
        'id',
        'user_id',
        'action_name',
        'function_name',
        'api',
        'ip',
        'device_name',
        'device_model',
        'device_grade',
        'device_type',
        'os_name',
        'os_ver',
        'os_type',
        'browser_name',
        'browser_ver',
        'browser_type',
        'miscellaneous'
     ];

     protected $casts = [
         'id' => 'string',
         'user_id' => 'integer',
     ];
     
    protected $table = 'accesshitories';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;
    /* public function getRouteKeyName()
    {
        return 'id';
    } */
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
    public function user(){
        return $this->hasOne('\App\Models\User', 'id', 'user_id');
    }
}