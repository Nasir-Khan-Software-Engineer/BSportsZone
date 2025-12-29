<?php

namespace App\Services\Category;

interface ICategoryService
{
    public function getAllCategories($posid);
    public function saveCategory($category);
    public function updateCategory($category);

    public function deleteCategory($posid, $id);
}
