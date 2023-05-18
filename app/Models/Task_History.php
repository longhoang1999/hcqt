<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task_History extends Model
{
    // use SoftDeletes;
    use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'task_id', 'description','user_id'
    ];
    protected $table = 'task_history';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;

   
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

     // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function users()
    {
        return $this->belongsToMany(User::class,'task_user','task_id','user_id');
    }
    public function project()
    {
        return $this->hasOne(Project::class, 'id','project_id');
    }
}
