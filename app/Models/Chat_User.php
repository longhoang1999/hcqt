<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Chat_User extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'chat_id',
        'user_id',
        'disabled_at',
        'left_at',
        'kick_at',
        'display_name',
     ];
     protected $casts = [
        'chat_id' => 'integer',
        'user_id' => 'integer',
     ];
     
    protected $table = 'chat_user';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function chat() {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
    public function userInf() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    

}