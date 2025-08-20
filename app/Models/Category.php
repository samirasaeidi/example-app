<?php

namespace App\Models;

use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'active',
    ];

    public function updateCategory(UpdateCategoryRequest $request, $name)
    {
        $name = $request->input('name');
        $category = $request->safe()->all() +
            [
                'active' => $request->input('active'),
                'slug' => createSlug($name),
            ];
        $this->fill($category)->save();
    }
}
