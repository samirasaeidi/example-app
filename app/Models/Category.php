<?php

namespace App\Models;

use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'active',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function updateCategory(UpdateCategoryRequest $request)
    {
        $category = $request->safe()->all();
        $this->fill($category)->save();
    }

    public function recursiveCategory(Category $category, string $breadCrumb): string
    {
        $breadCrumb = $category->name.' / '.$breadCrumb;
        if ($category->parent == null) {
            return $breadCrumb;
        }

        return $this->recursiveCategory($category->parent, $breadCrumb);
    }
}
