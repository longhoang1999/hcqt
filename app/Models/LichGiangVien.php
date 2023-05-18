<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichGiangVien extends Model
{
    
    
    protected $fillable = [
        'id',
        'lich_id',
        'outsite_lich_id',
        'giangvien_id',
        'teach_name',
        'room_id',
        'outsite_booking_room_id',
        'room_name',
        'campus_name',
        'branch_name',
        'study_date',
        'type_id'

     ];
          
     protected $casts = [
        'outsite_lich_id' => 'integer',
        'giangvien_id' => 'integer',
        'room_id' => 'integer',
        'lich_id' => 'integer',
        'outsite_booking_room_id' => 'integer',
        'type_id' => 'integer'
     ];
     
    protected $table = 'lich_giangviens';
    protected $primaryKey = 'id'; // or null
    public $timestamps = false;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function lich() {
         $this->belongsTo('\App\Models\Lich', 'id', 'lich_id');
    }

}