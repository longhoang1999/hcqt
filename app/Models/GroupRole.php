<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupRole extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $table = 'grouproles';
    protected $fillable = [
        'id',
        'code',
        'name',
        'desciption',
        'outsite_grouprole_id'
     ];
     
     protected $casts = [
        'id' => 'integer',
        'outsite_grouprole_id' => 'integer'
    ];
    public $timestamps = true;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function users() {
        return $this->belongsToMany(User::class, 'user_grouprole', 'user_id', 'grouprole_id');
    }
    public function roles() {
        return $this->belongsToMany(Role::class, 'grouprole_role', 'grouprole_id', 'role_id');
    }
}
