<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $table = 'roles';
    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'outsite_role_id'
     ];
     
     protected $casts = [
        'id' => 'integer',
        'outsite_role_id' => 'integer',
    ];
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;
    
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }
    public function grouproles() {
        return $this->belongsToMany(GroupRole::class, 'grouprole_role', 'grouprole_id', 'role_id');
    }
}
