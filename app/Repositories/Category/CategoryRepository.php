<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Carbon;

class CategoryRepository implements ICategoryRepository
{
    public function getAllCategories($posid)
    {
        return Category::join('users', 'users.id', '=', 'category.created_by')->select("category.*", "users.id as userId", "users.name as userName")->where('category.posid', $posid)->get();
    }

    public function saveCategory($category)
    {
        return Category::insert([
            'posid' => $category['posid'],
            'name' =>$category['name'],
            'icon' =>$category['icon'],
            'created_by' => $category['created_by'],
            'created_at' => Carbon\Carbon::now(),
        ]);

        //return Category::created($category);
    }

    public function updateCategory($category)
    {
        return Category::where('posid', $category['posid'])->where('id', $category['id'])->update([
            'name' =>$category['name'],
            'icon' =>$category['icon'],
            'updated_by' => $category['updated_by'],
            'updated_at' =>  Carbon\Carbon::now(),
        ]);
    }

    public function deleteCategory($posid, $id){
        return Category::where('posid', '=', $posid)->where('id', '=', $id)->delete();
    }
}
