<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Banner extends Model
{
    use \App\Traits\EditorsTrait;

    protected $table = 'banners';
    protected $fillable = [
        'id',
        'uploadfile_id'
     ];
    public $timestamps = true;
    
    public function image(){
        return $this->hasOne('\App\Models\UploadFile', 'id', 'uploadfile_id');
    }
}
