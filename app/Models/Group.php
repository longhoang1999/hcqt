<?php

namespace App\Models;

use App\Models\Group_User;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    // use SoftDeletes;
    use \App\Traits\EditorsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'ten_nhom', 'ma_nhom', 'mota','csdt_id', 'truong_nhom_id', 'group_chat_lock_at'
    ];
    protected $table = 'group';
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
        'csdt_id' => 'integer',
    ];

     // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    
    public function csdt() {
        return $this->belongsTo(\App\Models\Csdt::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class,'group_user','group_id','user_id');
    }
    public function chat()
    {
        return $this->hasOne(Chat::class, 'id','group_id');
    }

    public function lich()
    {
        return $this->hasOne(Lich::class,'source_chu_tri_id','id');
    }
    public function lichThamGia()
    {
        return $this->belongsToMany(Lich::class, 'lich_nguoithamgia', 'group_id', 'lich_id');
    }
    public function vanban()
    {
        return $this->hasOne(VanBan::class,'id','vanban_id');
    }
}
