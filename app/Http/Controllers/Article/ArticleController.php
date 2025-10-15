<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Jobs\VisitedArticle;
use App\Models\Article;
use App\Transformers\CategoryTransformer;
use League\Fractal\Manager;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use function Laravel\Prompts\search;

class ArticleController extends Controller
{
    const DEFAULT_SORT = 'id';

    const DEFAULT_DIRECTION = 'asc';


    public function seeArticle($id)
    {
        $article = Article::query()->find($id);
        $user = auth()->user();

        VisitedArticle::dispatch($article, $user);

        return response()->json([
            'status' => true,
            'message' => 'successfully',
            'article' => $article
        ]);
    }


    public function index(Request $request)
    {

        $allowedSortCollections = ['id', 'title', 'excerpt', 'status'];
        $articleQuery = Article::query();
        $sortInput = $request->input('sort', self::DEFAULT_SORT);

        if (is_string($sortInput)) {
            $articleQuery->orderBy($sortInput, self::DEFAULT_DIRECTION);
        }

        if (is_array($sortInput)) {
            $sortArticle = $allowedSortCollections[0] ?? self::DEFAULT_SORT;
            $sortDirection = $sortInput[1] ?? self::DEFAULT_DIRECTION;
            $sortDirection = in_array($sortDirection, ['asc', 'desc']) ?
                $sortDirection : self::DEFAULT_DIRECTION;
            $articleQuery->orderBy($sortArticle, $sortDirection);
        }

        $searchQuery = $request->input('search');
        if (!empty($searchQuery)) {
            $articleQuery->where('title', 'LIKE', '%' . $searchQuery . '%');
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
            'title' => 'string',
            'content' => 'string',
            'excerpt' => 'string',
            'status' => 'number',
        ];

        $preparedFilters = [
            [
                'name' => 'title',
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
                'name' => 'content',
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
                'name' => 'excerpt',
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
                'name' => 'status',
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

                        $value = $filter['value'] ?? null;

                        $logicalOperator = $filter['logical'] ?? 'AND';
                        if (!in_array($logicalOperator, $preparedFilter['logics'])) {
                            $logicalOperator = 'AND';
                        }
                        $logicalOperator = $availableLogical[$logicalOperator];

                        $operator = $filter['operator'] ?? 'EQ';
                        if (!in_array($operator, $preparedFilter['operators'])) {
                            $operator = 'EQ';
                        }

                        $fieldType = $fieldTypes[$field] ?? 'string';
                        if ($fieldType === 'number') {
                            $operator = $availableOperatorNumber[$operator];
                        } else {
                            $operator = $availableOperatorString[$operator];
                        }

                        if ($operator === 'like') {
                            $value = "%$value%";
                        }
                        $articleQuery->Where($field, $operator, $value, $logicalOperator);
                    }
                }
            }
        }

        $perPage = $request->input('per_page', 12);
        $paginator = $articleQuery->paginate($perPage);
        $fractal = new Manager();
        $resource = new Collection($paginator->items(), new ArticleTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $article = $fractal->createData($resource)->toArray();

        return $this->createResponse(
            true,
            'Articles found successfully',
            $article

        );
    }

    public function store(CreateArticleRequest $request)
    {
        $article = Article::query()->create(
            $request->safe()->all()
        );
        return $this->createResponse(true, 'Article Create Successfully', new ArticleResource($article));
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $fractal = new Manager();
        $resource = new Item($article, new ArticleTransformer());
        $article->update(
            $request->safe()->all()
        );
        return $this->createResponse(
            true,
            'Article Updated Successfully',
            $fractal->createData($resource)->toArray()
        );
    }

    public function show(Article $article)
    {
//        $article = Article::query()->find($article);
        $user = auth()->user();
        VisitedArticle::dispatch($article, $user);

        $fractal = new Manager();
        $resource = new Item($article, new ArticleTransformer());
        return $this->createResponse(
            true,
            'Article Found Successfully',
            $fractal->createData($resource)->toArray()
        );
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return $this->createResponse(true, 'Article Deleted Successfully');
    }

    protected function createResponse(bool $status, string $message, $article = [])
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'article' => $article
        ]);
    }
    protected function responseFailed($message)
    {
        return $this->createResponse(false, $message);
    }
}




