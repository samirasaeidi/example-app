<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;


class AdminCntroller extends Controller
{

    protected const DEFAULT_SORT = 'id';
    protected const DEFAULT_DIRECTION = 'asc';

    protected const DEFAULT_FILTER = 'id';


    public function index(IndexUserRequest $request)
    {
        $allowedCollections = ['mobile', 'national_code', 'id', 'first_name'];

        $userQuery = User::query();
        $orderInput = $request->input('sort', self::DEFAULT_SORT);
        if (is_string($orderInput)) {
            $userQuery->orderBy($orderInput, self::DEFAULT_DIRECTION);
        } elseif (is_array($orderInput)) {
            $orderColumn = $allowedCollections[0] ?? self::DEFAULT_SORT;
            $orderDirection = $orderInput[1] ?? self::DEFAULT_DIRECTION;
            $orderDirection = in_array($orderDirection, ['asc', 'desc']) ?
                $orderDirection : self::DEFAULT_DIRECTION;
            $userQuery->orderBy($orderColumn, $orderDirection);
        }

        $searchQuery = $request->input('search');
        if (!empty($searchQuery)) {
            $userQuery->where(function ($q) use ($searchQuery) {
                $q->where('first_name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('national_code', 'LIKE', '%' . $searchQuery . '%');
            });
        }


        $filters = $request->input('filters', []);

        $availableOperatorString = [
            'LIKE' => 'like',
            'NOT' => '!=',
            'EQ' => '='
        ];

        $availableOperatorNumber = [
            'EQ' => '=',
            'GT' => '>',
            'GTE' => '>=',
            'LT' => '<',
            'LTE' => '=<',
            'NOT' => '!='
        ];

        $availableLogical = [
            'AND' => 'and',
            'OR' => 'or'
        ];

        $fieldTypes = [
            'age' => 'number',
            'first_name' => 'string',
            'last_name' => 'string',
            'mobile' => 'string',
            'father_name' => 'string',
            'national_code' => 'string'
        ];


        $preparedFilters = [
            [
                'name' => 'first_name',
                'logics' => [
                    'AND',
                    'OR'
                ],
                'type' => 'string',
                'operators' => [
                    'LIKE',
                    'NOT',
                    'EQ'
                ]
            ],
            [
                'name' => 'last_name',
                'logics' => [
                    'AND',
                    'OR'
                ],
                'type' => 'string',
                'operators' => [
                    'LIKE',
                    'NOT',
                    'EQ'
                ],
            ],
            [
                'name' => 'father_name',
                'logics' => [
                    'AND',
                    'OR'
                ],
                'type' => 'string',
                'operators' => [
                    'LIKE',
                    'NOT',
                    'EQ'
                ],
            ],
            [
                'name' => 'age',
                'logics' => [
                    'AND',
                    'OR'
                ],
                'type' => 'number',
                'operators' => [
                    'NOT',
                    'EQ',
                    'GT',
                    'GTE',
                    'LT',
                    'LTE'
                ]
            ],
            [
                'name' => 'national_code',
                'logics' => [
                    'AND',
                    'OR'
                ],
                'type' => 'string',
                'operators' => [
                    'LIKE',
                    'NOT',
                    'EQ'
                ]
            ],
            [
                'name' => 'mobile',
                'logics' => [
                    'AND',
                    'OR'
                ],
                'type' => 'string',
                'operators' => [
                    'LIKE',
                    'NOT',
                    'EQ'
                ]
            ]
        ];

        foreach ($filters as $field => $fieldFilters) {

            foreach ($preparedFilters as $preparedFilter) {

                if ($preparedFilter['name'] === $field) {

                    foreach ($fieldFilters as $filter) {

                        $value = $filter['value'] ?? null;

                        $logicalOperator = $filter['logical'] ?? 'AND';
                        if (!in_array($logicalOperator, $preparedFilter['logics'])) {
                            $logicalOperator= 'AND';
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
                        $userQuery->Where($field, $operator, $value, $logicalOperator);
                    }
                }
            }
        }

        $perPage = $request->input('per_page', 15);
        $user = $userQuery->paginate($perPage);
        return $this->createResponse(true, 'Users found successfully', $user);
    }


    public function show($id)
    {
        $user = User::query()->findOrFail($id);
        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }
        return $this->createResponse(true, 'User found successfully.', $user);

    }

    public function store(CreateUserRequest $request)
    {
        $mobile = $request->input('mobile');
        $user = User::query()->where('mobile', $mobile)->first();

        if ($user) {
            return $this->responseFailed('The user has already been created.');
        }
        $user = User::query()->Create(
            $request->safe()->all() +
            [
                'birth_date' => $request->input('birth_date'),
                'father_name' => $request->input('father_name'),
            ]
        );
        return $this->createResponse(true, 'User created successfully.', $user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::query()->find($id);
        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }

        $user->updateUser($request);

        return $this->createResponse(true, 'User information edited successfully.', $user);
    }

    public function destroy($id)
    {
        $user = User::query()->find($id);
        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }
        $user->delete();
        return $this->createResponse(true, 'User deleted successful.');
    }

    private function createResponse(bool $status, string $message, $user = [])
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $user
        ]);
    }

    private function responseFailed($message)
    {
        return $this->createResponse(false, $message);
    }
}

//public function index(IndexUserRequest $request)
//{
//    $allowedCollections = ['mobile', 'national_code', 'id', 'first_name'];
//
//    $userQuery = User::query();
//    $orderInput = $request->input('sort', self::DEFAULT_SORT);
//    if (is_string($orderInput)) {
//        $userQuery->orderBy($orderInput, self::DEFAULT_DIRECTION);
//    } elseif (is_array($orderInput)) {
//        $orderColumn = $allowedCollections[0] ?? self::DEFAULT_SORT;
//        $orderDirection = $orderInput[1] ?? self::DEFAULT_DIRECTION;
//        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ?
//            $orderDirection : self::DEFAULT_DIRECTION;
//        $userQuery->orderBy($orderColumn, $orderDirection);
//    }
//
//    $searchQuery = $request->input('search');
//    if (!empty($searchQuery)) {
//        $userQuery->where(function ($q) use ($searchQuery) {
//            $q->where('first_name', 'LIKE', '%' . $searchQuery . '%')
//                ->orWhere('last_name', 'LIKE', '%' . $searchQuery . '%')
//                ->orWhere('mobile', 'LIKE', '%' . $searchQuery . '%')
//                ->orWhere('national_code', 'LIKE', '%' . $searchQuery . '%');
//        });
//    }
//
//
//    $filters = $request->input('filters', []);
//
//    $availableOperatorString = [
//        'LIKE' => 'like',
//        'NOT' => '!=',
//        'EQ' => '='
//    ];
//
//    $availableOperatorNumber = [
//        'EQ' => '=',
//        'GT' => '>',
//        'GTE' => '>=',
//        'LT' => '<',
//        'LTE' => '=<',
//        'NOT' => '!='
//    ];
//
//    $availableLogical = [
//        'AND' => 'and',
//        'OR' => 'or'
//    ];
//
//    $fieldTypes = [
//        'age' => 'number',
//        'first_name' => 'string',
//        'last_name' => 'string',
//        'mobile' => 'string',
//        'father_name' => 'string',
//        'national_code' => 'string'
//    ];
//
//
//    $preparedFilters = [
//        [
//            'name' => 'first_name',
//            'logics' => [
//                'AND',
//                'OR'
//            ],
//            'type' => 'string',
//            'operators' => [
//                'LIKE',
//                'NOT',
//                'EQ'
//            ]
//        ],
//        [
//            'name' => 'last_name',
//            'logics' => [
//                'AND',
//                'OR'
//            ],
//            'type' => 'string',
//            'operators' => [
//                'LIKE',
//                'NOT',
//                'EQ'
//            ],
//        ],
//        [
//            'name' => 'father_name',
//            'logics' => [
//                'AND',
//                'OR'
//            ],
//            'type' => 'string',
//            'operators' => [
//                'LIKE',
//                'NOT',
//                'EQ'
//            ],
//        ],
//        [
//            'name' => 'age',
//            'logics' => [
//                'AND',
//                'OR'
//            ],
//            'type' => 'number',
//            'operators' => [
//                'NOT',
//                'EQ',
//                'GT',
//                'GTE',
//                'LT',
//                'LTE'
//            ]
//        ],
//        [
//            'name' => 'national_code',
//            'logics' => [
//                'AND',
//                'OR'
//            ],
//            'type' => 'string',
//            'operators' => [
//                'LIKE',
//                'NOT',
//                'EQ'
//            ]
//        ],
//        [
//            'name' => 'mobile',
//            'logics' => [
//                'AND',
//                'OR'
//            ],
//            'type' => 'string',
//            'operators' => [
//                'LIKE',
//                'NOT',
//                'EQ'
//            ]
//        ]
//    ];
//
//    foreach ($filters as $field => $fieldFilters) {
//
//        foreach ($fieldFilters as $filter) {
//
//            $value = $filter['value'] ?? null;
//            $logicalOperator = $availableLogical[$filter['logical'] ?? 'AND'];
//            $fieldType = $fieldTypes[$field] ?? 'string';
//
//            if ($fieldType === 'string') {
//                $operator = $availableOperatorString[$filter['operator'] ?? 'EQ'];
//            } elseif ($fieldType === 'number') {
//                $operator = $availableOperatorNumber[$filter['operator'] ?? 'EQ'];
//            }
//            if ($operator === 'like') {
//                $value = "%$value%";
//            }
//            $userQuery->Where($field, $operator, $value, $logicalOperator);
//        }
//    }
//
//    $perPage = $request->input('per_page', 15);
//    $user = $userQuery->paginate($perPage);
//    return $this->createResponse(true, 'Users found successfully', $user);
//}




