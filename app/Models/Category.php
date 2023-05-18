<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $fillable = [
        'code',
        'name',
        'description'
     ];
     
    protected $table = 'categories';
    protected $primaryKey = 'code'; // or null

    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

    public function posts(){
        return $this->belongsToMany('App\Models\Post', 'category_post', 'category_cd', 'post_id');
        //->withPivot('column1', 'column2');
    }

    public function getbyCd($cd){
        return Category::find($cd);
    }

    public function insertOrUpdate($cd, $name, $description){
        /* $post = Post::updateOrCreate(
            ['code', $cd],
            [
                'name' => $name,
                'description' => $description,
            ]
        );
        return $post; */
    }

    public static function deleteByCd($cd){
        Category::where('code', $cd)->delete();
    }
}