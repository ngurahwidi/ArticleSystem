<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Support\Str;
use App\Models\Component\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Article\Traits\HasActivityArticleProperty;

class Article extends BaseModel
{
    use HasActivityArticleProperty;
    
    protected $table = 'articles';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
        'gallery' => 'array'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_categories', 'articleId', 'categoryId');
    }
    

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = Article::createSlug($article->title);
            }
        });
    }

    public static function createSlug($title)
    {
        $slug = Str::slug($title);
        $count = Article::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }
}
