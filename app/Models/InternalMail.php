<?php

namespace App\Models;

use App\Common\Constant\UploadFileConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
class InternalMail extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'subject',
        'keyword',
        'status',
        'body',
        'creator_id',
        'sent_date',
        'tos',
        'bccs',
        'ccs',
        'reply_mail_id',
        'forward_mail_id',
        'source_type'
     ];
     protected $casts = [
        'status' => 'integer',
        'creator_id' => 'integer',
        'forward_mail_id' => 'integer',
        'source_type' => 'integer',
        'tos' => 'array',
        'bccs' => 'array',
        'ccs' => 'array',
        'reply_mail_ids' => 'array'
     ];
     
    protected $table = 'internalmails';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function tos(){
        return $this->belongsToMany('App\Models\User', 'internalmail_user', 'id', 'internalmail_id')
        ->withPivot('star_flg', 'read_at', 'from_folder');
    }

    public function bccs(){
        return $this->belongsToMany('App\Models\User', 'internalmail_user', 'id', 'internalmail_id')
        ->withPivot('star_flg', 'read_at', 'from_folder');
    }

    public function ccs(){
        return $this->belongsToMany('App\Models\User', 'internalmail_user', 'id', 'internalmail_id')
        ->withPivot('star_flg', 'read_at', 'from_folder');
    }
   
    public function attachedFiles(){
        return $this->hasMany('\App\Models\UploadFile', 'fk_id', 'id')->where('category', UploadFileConstants::INTERNAL_MAIL);
    }
    public function nguoigui() {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }
    public function isReply() {
        return $this->source_type == 1;

    }
    public function replyMails(){
        return InternalMail::find($this->forward_mail_id);
        
    }
    public function isForward() {
        return $this->source_type == 2;
    }
    public function forwardMail(){
        return InternalMail::find($this->reply_mail_ids);
        
    }
    public function receivers(){
        return $this->belongsToMany('\App\Models\User', 'internalmail_user', 'internalmail_id', 'user_id')
        ->withPivot('donvi_id', 'star_flg', 'read_at', 'from_folder', 'receiver_type');
        
    }
}