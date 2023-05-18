<?php

namespace App\Models;

use App\Common\Constant\NotificationConstants;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    //use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'content',
        'bigcategory',
        'category',
        'subcategory',
        'subcategory',
        'source_id', 
        'receiver_id',
        'receiver_type',
        'status',
        'read_at',
        'sent_at',
        'creator_id'
     ];
     protected $casts = [
        'bigcategory' => 'integer',
        'category' => 'integer',
        'subcategory' => 'integer',
        'source_id' => 'integer',
        'receiver_id' => 'integer',
        'receiver_type' => 'integer',
        'creator_id' => 'integer',
        'status' => 'integer',
     ];
     
     
    protected $table = 'notifications';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    protected $appends = ['big_category_name'];

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function getBigCategoryNameAttribute () {
    
        if ($this->bigcategory == NotificationConstants::BIG_CATEGORY_LICH) {
            return "Lịch";
        } else if ($this->bigcategory == NotificationConstants::BIG_CATEGORY_VAN_BAN) {
            return "Văn bản";
        }
        return "";
    
    }

}