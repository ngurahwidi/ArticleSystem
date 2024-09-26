<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Models\Article\Article;
use App\Models\Comment\Comment;
use App\Models\Component\Category;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;

    protected $table = 'users';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    public function articles()
    {
        return $this->hasMany(Article::class, 'userId');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'userId');
    }

    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'favorites', 'userId', 'articleId');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'userId');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
