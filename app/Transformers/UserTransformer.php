<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;


class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'mobile' => $user->mobile,
            'password' => $user->password,
            'national_code' => $user->national_code,
            'age' => $user->age,
            'birth_date' => $user->birth_date,
            'active' => $user->active
        ];

    }

}
