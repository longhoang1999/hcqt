<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class RoomBooking extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
    
    protected $fillable = [
        'id',
        'room_id',
        'lich_id',
        'outsite_booking_room_id'
     ];
     protected $casts = [
        'room_id' => 'integer',
        'lich_id' => 'integer',
        'outsite_booking_room_id' => 'integer',
     ];
     
    protected $table = 'room_booking';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    

}