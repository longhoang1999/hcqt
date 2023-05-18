<?php

namespace App\Models;

use App\Common\Constant\UploadFileConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    // use SoftDeletes;
    use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description', 'is_important', 'assign_id', 'is_urgent', 'is_notification', 'status'
    ];
    protected $table = 'project';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('c');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
            ->withPivot('project_id', 'user_id', 'is_main');
    }
    public function task()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }
    public function attachedFiles(){
        return $this->hasMany('\App\Models\UploadFile', 'fk_id', 'id')
        ->where('source', UploadFileConstants::PROJECT)
        ->where('category', UploadFileConstants::CATEGORY_PROJECT_KET_QUA_THUC_HIEN);
    }
}
