<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Category;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category)
    {
        return [
            'id' =>$category->id,
            'name' =>(string) $category->name,
            'slug' =>(string) $category->slug,
            'active' =>(boolean) $category->active,
            'parent_id' =>$category->parent_id,
            'breadcrumb' => $category->parent ? $category->recursiveCategory($category->parent, $category->name) : $category->name,
        ];
    }
}
