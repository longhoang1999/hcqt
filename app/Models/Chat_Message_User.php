<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Common\Constant\ChattingConstants;
use App\Models\AppUser_Message;

class Chat_Message_User extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'user_id',
        'chat_id',
        'message_id',
        'read_at',
     ];

     protected $casts = [
        'user_id' => 'integer',
        'chat_id' => 'integer',
        'message_id' => 'float',
        
     ];
     
    protected $table = 'chat_message_user';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function message()
    {
        return $this->hasOne('\App\Models\Chat_Message', 'id', 'message_id');
    }
    public function userInf()
    {
        return $this->belongsTo('\App\Models\User', 'user_id', 'id');
    }
    
    public function messageAppUser()
    {
        return $this->hasOne('\App\Models\AppUser_Message', 'user_message_id', 'id');
    }
    
}