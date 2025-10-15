<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Events\UserRegistered;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'password',
        'is_ban',
        'national_code',
        'birth_date',
        'father_name',
        'age',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot(){
        parent::boot();

        static::created(function (User $user) {
            UserRegistered::dispatch();
        });
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $user = $request->safe()->all() +
            [
                'birth_date' => $request->input('birth_date'),
                'father_name' => $request->input('father_name'),
            ];
        $this->fill($user)->save();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_users', 'user_id', 'article_id')->using(ArticleUser::class);
    }
}
