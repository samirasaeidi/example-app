<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleUser extends Pivot
{
    protected $fillable = [
        'user_id',
        'article_id',
        'visited_count',
        'visited_at'
    ];
}
