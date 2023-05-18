<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class InternalMail_User extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'internalmail_id',
        'user_id',
        'donvi_id',
        'star_flg',
        'read_at',
        'from_folder'
     ];
     protected $casts = [
        
     ];
     
    protected $table = 'internalmail_user';
    //protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function receiver(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function attachedFiles(){
        return $this->hasOne('\App\Models\UploadFile', 'id', 'fk_id');
        
    }
    public function InternalMail(){
        return $this->hasOne('App\Models\InternalMail', 'id', 'internalmail_id');
    }
   
    public function read_flg(){
       return isset($this->read_at) && $this->read_at != null;
    }

}