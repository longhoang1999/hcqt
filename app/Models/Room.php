<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    use \App\Traits\EditorsTrait;
    //use SoftDeletes;
            
    protected $fillable = [
        'id',
        'room_name',
        'outsite_room_id',
        'department_id',
        'branch_id',
        'branch_name',
        'campus_id', 
        'campus_name',
        'roompurpose_idmain',
        'examzoom_code',
        'capacity',
        'floor',
        'mota'
     ];
     protected $casts = [
        'outsite_room_id' => 'integer',
        'department_id' => 'integer',
        'branch_id' => 'integer',
        'campus_id' => 'integer',
        'roompurpose_idmain' => 'integer',
        'capacity' => 'integer',
        'floor' => 'integer',
        
        
     ];
     
    protected $table = 'rooms';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_booking', 'lich_id', 'room_id')
            ->withPivot('lich_id', 'room_id');
    }

}