<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LichResult extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $fillable = [
        'id',
        'lich_id',
        'content',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
     ];
     protected $casts = [
        'lich_id'       => 'integer'
     ];
     
    protected $table = 'lich_results';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

}
