<?php

namespace App\Repositories\Category;

interface ICategoryRepository
{
    public function getAllCategories($posid);
    public function saveCategory($category);
    public function updateCategory($category);
    public function deleteCategory($posid, $id);
}
