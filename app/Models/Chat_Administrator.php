<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Chat_Administrator extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'chat_id',
        'admin_id',
     ];
     protected $casts = [
         'admin_id' => 'integer',
        'chat_id' => 'integer',
     ];
     
    protected $table = 'chat_administrators';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function user()
    {
        return $this->hasOne('\App\Models\user', 'id', 'admin_id');
    }
    public function chat()
    {
        return $this->hasOne('\App\Models\chat', 'id', 'chat_id');
    }
}