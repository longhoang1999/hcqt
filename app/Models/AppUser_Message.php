<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\AppUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AppUser_Message extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'senderId',
        'receiverId',
        'registrationId',
        'body',
        'type',
        'sender_deviceId',
        'receiver_deviceId',
        'content_id',
        'content_type',
        'vect',
        'salt',
        'user_message_id'

    ];
    protected $casts = [
        'id' => 'string',
        'senderId' => 'integer',
        'receiverId' => 'integer',
        'registrationId' => 'integer',
        'sender_deviceId' => 'integer',
        'receiver_deviceId' => 'integer',
        'content_id' => 'integer',
        'content_type' => 'integer',
    ];
    protected $appends = ['message_from', 'message_to'];

    protected $table = 'appuser_message';
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
    
    public function getMessageFromAttribute() {
        return AppUser::where('userId', $this->senderId)
                ->first();
    }
    public function getMessageToAttribute() {
        return AppUser::where('userId', $this->receiverId)
                ->first();
    }
    

}