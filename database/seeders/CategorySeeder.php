<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=1; $i<=15; $i++){
            $category = new Category;
            $category->posid = 1;
            $category->name = "Category-".$i;
            $category->icon = "demo";
            $category->created_by = 1;
            $category->save();
        }
    }
}
