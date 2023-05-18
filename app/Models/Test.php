<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    //
    protected $fillable = [
        'id', 'random_string'
    ];
    protected $table = 'tests';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;
    
}