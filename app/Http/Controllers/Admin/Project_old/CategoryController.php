<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function insertOrUpdate(CategoryRequest $request){
        
        if(isset($request->category_cd)) {
            $category = Category::find($request->category_cd);
        }
        if (!isset($category)) {
            $category = new Category;
            $new_category = Category::orderBy('code', 'desc')->first();
            $request->category_cd = $new_category->code +1;
        }
        $category->code = $request->category_cd;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->icon = null;
        $category->is_delete = $request->is_delete;
        try{
            $category->save();
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return $this->responseJson([
            'categoty' => $category
            ]);
    }

    public function delete(Request $request){
        Category::deleteByCd($request->category_cd);
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }

    public function getCategory($category_cd = '0') {
        
        $category = Category::find($category_cd);
        return $this->responseJson(['category'=>$category]
        );
    }

    public function getCategoryWithPost(Request $request, Category $category, $currentPost_id = 0) {
        $category_cd = $request->category_cd;
        $category = Category::find($category_cd);
        if (isset($category)) {
            $category->load(['posts']);
        }
        return $this->responseJson(['caterogy'=>$category]
        );
    }
    public function getCategories() {
        try {
            $categories = Category::orderBy('code')->get();
            return $this->responseJson(['categories'=>$categories]);
        }catch(Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
        
    }
    public function getCategoriesWithPost() {
        $categories = Category::all()->orderBy('name')->get();
        $categories->load('posts');
        
        return $this->responseJson(['categories'=>$categories]
        );
    }
}
