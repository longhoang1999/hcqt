<?php

namespace App\Models;

use App\Models\LinhVucVanBan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Chat_Quote_Message extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'message_id',
        'message_quote_id',
        'quote_content',
     ];
     protected $casts = [
         'id' => 'integer',
         'message_id' => 'integer',
     ];
     
    protected $table = 'chat_quote_message';
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
        return $this->hasOne('\App\Models\Chat_Message', 'id', 'message_quote_id');
    }
}