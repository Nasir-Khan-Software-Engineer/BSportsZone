<?php

namespace App\Repositories\Category;

interface ICategoryRepository
{
    public function getAllCategories($POSID);
    public function saveCategory($category);
    public function updateCategory($category);
    public function deleteCategory($POSID, $id);
}
