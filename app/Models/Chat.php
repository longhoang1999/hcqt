<?php

namespace App\Models;

use App\Services\ChatService;
use App\Models\Chat_Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Chat extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'name',
        'is_group',
        'avatar_id',
        'group_chat_type',
        'group_id',
        'wall_backgroup',
        'group_chat_lock_at',
        'group_chat_lock_at_by_admin'
     ];
     protected $casts = [
         'donvi_id' => 'integer',
        'avatar_id' => 'integer',
        'is_group' => 'boolean'
     ];
     
    protected $table = 'chats';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;
    protected $appends = ['members', 'active_members', 'member_me', 'administrators'];

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

   
    public function getMembersAttribute()
    {
        try {
            return ChatService::getListMembersOfChat($this);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return \collect([]);
        }
    }
    public function getActiveMembersAttribute()
    {
        try {
            return ChatService::getListActiveMembersOfChat($this);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return \collect([]);
        }
    }
    public function getMemberMeAttribute()
    {
        try {
            return ChatService::getMemberMeOfChat($this);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
    public function allMessages()
    {
        return $this->hasMany('\App\Models\Chat_Message', 'id', 'chat_id');
    }
    public function avatar()
    {
        return $this->hasOne('\App\Models\UploadFile', 'fk_id', 'avatar_id');
    }
    public function getAdministratorsAttribute()
    {
        try {
        return Chat_Administrator::where('chat_id', $this->id)->get();
        }catch (Exception $e) {
            error_log($e->getMessage());
            return \collect([]);
        }
    }
}