<?php

namespace App\Services\Category;

use App\Repositories\Category\ICategoryRepository;

class CategoryService implements ICategoryService
{
    public function __construct(ICategoryRepository $iCategoryRepository)
    {
        $this->categoryRepository = $iCategoryRepository;
    }

    public function getAllCategories($POSID){
        return $this->categoryRepository->getAllCategories($POSID);
    }

    public function saveCategory($category){
        return $this->categoryRepository->saveCategory($category);
    }

    public function updateCategory($category){
        return $this->categoryRepository->updateCategory($category);
    }

    public function deleteCategory($POSID, $id)
    {
        return $this->categoryRepository->deleteCategory($POSID, $id);
    }
}
