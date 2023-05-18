<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Csdt extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $fillable = [
        'id',
        'ma_csdt',
        'ten_csdt',
        'dia_chi',
        'img_logo',
        'sdt_lienhe',
        'ns_phutrach',
        'primary_key',
        'trang_thai',
        'han_noptien',
     ];
     protected $casts = [
        'status' => 'integer',
        'creator_id' => 'integer',
        'publicer_id' => 'integer'
     ];
     
    protected $table = 'csdt';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }


}