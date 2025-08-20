<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategory;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected const DEFAULT_SORT = 'id';

    protected const DEFAULT_DIRECTION = 'asc';

    public function index(IndexCategoryRequest $request)
    {

        $allowedSortCollections = ['name', 'active', 'slug'];
        $categoryQuery = Category::query();
        $sortInput = $request->input('sort', self::DEFAULT_SORT);

        if (is_string($sortInput)) {
            $categoryQuery->orderBy($sortInput, self::DEFAULT_DIRECTION);
        }
        if (is_array($sortInput)) {
            $sortCategory = $allowedSortCollections[0] ?? self::DEFAULT_SORT;
            $sortDirection = $sortInput[1] ?? self::DEFAULT_DIRECTION;
            $sortDirection = in_array($sortDirection, ['asc', 'desc']) ?
                $sortDirection : self::DEFAULT_DIRECTION;
            $categoryQuery->orderBy($sortCategory, $sortDirection);
        }

        $searchQuery = $request->input('search');
        if (! empty($searchQuery)) {
            $categoryQuery->where('name', 'LIKE', '%'.$searchQuery.'%');
        }

        $filters = $request->input('filters', []);

        $availableOperatorString = [
            'LIKE' => 'like',
            'NOT' => '!=',
            'EQ' => '=',
        ];

        $availableOperatorNumber = [
            'EQ' => '=',
            'NOT' => '!=',
            'GT' => '>',
            'GTE' => '>=',
            'LT' => '<',
            'LTE' => '=<',
        ];

        $availableLogical = [
            'AND' => 'and',
            'OR' => 'or',
        ];

        $fieldTypes = [
            'name' => 'string',
            'slug' => 'string',
            'parent_id' => 'number',
            'active' => 'number',
        ];

        $preparedFilters = [
            [
                'name' => 'name',
                'logics' => [
                    'AND',
                    'OR',
                ],
                'type' => 'string',
                'operators' => [
                    'LIKE',
                    'NOT',
                    'EQ',
                ],
            ],
            [
                'name' => 'slug',
                'logics' => [
                    'AND',
                    'OR',
                ],
                'type' => 'string',
                'operators' => [
                    'LIKE',
                    'NOT',
                    'EQ',
                ],
            ],
            [
                'name' => 'parent_id',
                'logics' => [
                    'AND',
                    'OR',
                ],
                'type' => 'number',
                'operators' => [
                    'EQ',
                    'NOT',
                    'GT',
                    'GTE',
                    'LT',
                    'LTE',
                ],
            ],
            [
                'name' => 'active',
                'logics' => [
                    'AND',
                    'OR',
                ],
                'type' => 'number',
                'operators' => [
                    'EQ',
                    'NOT',
                    'GT',
                    'GTE',
                    'LT',
                    'LTE',
                ],
            ],
        ];

        foreach ($filters as $field => $fieldFilters) {

            foreach ($preparedFilters as $preparedFilter) {

                if ($preparedFilter['name'] === $field) {

                    foreach ($fieldFilters as $filter) {

                    }
                }
            }
        }

        $prePage = $request->input('pre_page', 12);
        $category = $categoryQuery->paginate($prePage);

        return $this->createResponse(true, 'Categories found successfully', $category);

    }

    public function store(CreateCategory $request)
    {
        $name = $request->input('name');
        $slug = Str::slug($request->name, '-');

        $category = Category::query()->where('name', $name)->first();

        if ($category) {
            return $this->responseFailed('Category was exist.');
        }

        $category = Category::query()->create(
            $request->safe()->all() +
            [
                'parent_id' => $request->input('parent_id'),
                'active' => $request->input('active'),
                'slug' => createSlug($name),
            ]
        );

        return $this->createResponse(true, 'Create Category Was Successfully', $category);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::query()->find($id);

        if (! $category) {
            return $this->responseFailed('Category does not exist');
        }

        $category->updateCategory($request);

        return $this->createResponse(true, 'Category was Update Successfully.', $category);
    }

    public function destroy($id)
    {
        $category = Category::query()->find($id);

        if (! $category) {
            return $this->responseFailed('Category does not exist.');
        }
        $category->delete();

        return $this->createResponse(true, 'Category deleted successfully');
    }

    public function show($id)
    {
        $category = Category::query()->findOrFail($id);

        if (! $category) {
            return $this->responseFailed('Category does not exist.');
        }

        return $this->createResponse(true, 'Category found successfully.', $category);
    }

    private function responseFailed($message)
    {
        return $this->createResponse(false, $message);

    }

    private function createResponse(bool $status, string $message, $category = [])
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'category' => $category,
        ]);

    }
}
