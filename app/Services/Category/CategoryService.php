<?php

namespace App\Services\Category;

use App\Repositories\Category\ICategoryRepository;

class CategoryService implements ICategoryService
{
    public function __construct(ICategoryRepository $iCategoryRepository)
    {
        $this->categoryRepository = $iCategoryRepository;
    }

    public function getAllCategories($posid){
        return $this->categoryRepository->getAllCategories($posid);
    }

    public function saveCategory($category){
        return $this->categoryRepository->saveCategory($category);
    }

    public function updateCategory($category){
        return $this->categoryRepository->updateCategory($category);
    }

    public function deleteCategory($posid, $id)
    {
        return $this->categoryRepository->deleteCategory($posid, $id);
    }
}
