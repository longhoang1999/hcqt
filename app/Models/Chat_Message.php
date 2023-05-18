<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Chat_Message extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'chat_id',
        'sender_id',
        'type',
        'content',
        'action',
        'recall_at',
        'file_id',
        'vect',
        'salt',
        'encryptType',
        'expiredAfterMinute',
        'notifity_type'
     ];
     protected $casts = [
         'chat_id' => 'integer',
         'sender_id' => 'integer',
         'action' => 'integer',
         'file_id' => 'integer'
     ];
     
    protected $table = 'chat_message';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;
    //protected $appends = ['appUserMessage'];

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function chat()
    {
        return $this->belongsTo('\App\Models\Chat', 'chat_id', 'id');
    }

    public function message_users()
    {
        return $this->hasMany('\App\Models\Chat_Message_User', 'message_id', 'id');
    }
    public function quote_message()
    {   
        return $this->belongsTo('\App\Models\Chat_Quote_Message', 'id', 'message_id');
    }
    public function sender()
    {
        return $this->belongsTo('\App\Models\User', 'sender_id', 'id');
    }
    public function attachFile()
    {
        return $this->belongsTo('\App\Models\UploadFile', 'file_id', 'id');
    }
    
    public function appUserMessage()
    {
        return $this->belongsTo('\App\Models\AppUser_Message', 'user_message_id', 'id');
    }
    
    
}