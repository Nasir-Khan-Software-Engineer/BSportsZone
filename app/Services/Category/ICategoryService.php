<?php

namespace App\Services\Category;

interface ICategoryService
{
    public function getAllCategories($POSID);
    public function saveCategory($category);
    public function updateCategory($category);

    public function deleteCategory($POSID, $id);
}
