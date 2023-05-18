<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\AppUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AppUser_Prekey extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'appuser_id',
        'keyId',
        'publicKey',
     ];
     protected $casts = [
        'id' => 'string',
        'appuser_id' => 'string',
     ];
     

    protected $table = 'appuser_prekey';
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
    
    public function appuser() {
        return $this->belongsTo(AppUser::class, 'appuser_id', 'id');
    }
    

}