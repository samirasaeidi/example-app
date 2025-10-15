<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'status',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'article_users','article_id','user_id')->using(ArticleUser::class);
    }
}
