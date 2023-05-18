<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupRole_Role extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $table = 'grouprole_role';
    protected $fillable = [
        'id',
        'grouprole_id',
        'role_id'
     ];
     
     protected $casts = [
        'grouprole_id' => 'integer',
        'role_id' => 'integer'
    ];
    public $timestamps = true;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
}
