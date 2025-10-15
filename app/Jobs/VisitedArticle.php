<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Support\Facades\Log;

class VisitedArticle implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public User    $user
    )
    {
        $this->queue = 'visit_article';
    }

    public function handle(): void
    {
        $visited_count = $this->article->users()->increment('visited_count');

        if ($this->article->users()->where('user_id', $this->user->id)->exists()) {
            $this->article->users()->updateExistingPivot($this->user->id, [
                'visited_at' => now(),
                'visited_count' => $visited_count
            ], false);
        } else {
            $this->article->users()->attach($this->user->id, [
                'visited_at' => now(),
                'visited_count' => 1
            ]);
        }

//        Log::debug('this job has been attempted'.$this->attempts().'times');
    }

}


