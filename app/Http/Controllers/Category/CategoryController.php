<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategory;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(IndexCategoryRequest $request)
    {
        dd(1);
    }

    public function store(CreateCategory $request)
    {
        $name = $request->input('name');
        $category = Category::query()->where('name', $name)->first();

        if ($category) {
            return response()->json([
                'status' => false,
                'message' => 'Category was exist.'
            ]);
        }

        $category = Category::query()->create(
            $request->safe()->all() +
            [
                'parent_id' => $request->input('parent_id'),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Create Category Was Successfully',
            'data' => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request,$id)
    {
        $category=Category::query()->first($id);

        if(!$category){
            return response()->json([
               'status'=>false,
               'message'=>'User does not exist'
            ]);
        }

        $category->updateCategory($request);

        return response()->json([
            'status'=>true,
            'message'=>'Category was Update Successfully.'
        ]);



    }

}
