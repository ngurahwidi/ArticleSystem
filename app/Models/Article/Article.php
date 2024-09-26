<?php

namespace App\Models\Article;

use Carbon\Carbon;
use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Support\Str;
use App\Models\Component\Tag;
use App\Models\Component\Category;
use App\Parser\Article\ArticleParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Article\Traits\HasActivityArticleProperty;
use App\Models\Comment\Comment;

class Article extends BaseModel
{
    use HasActivityArticleProperty;
    
    protected $table = 'articles';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
        'galleries' => 'array'
    ];

    public $parserClass = ArticleParser::class;

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'component_article_categories', 'articleId', 'categoryId');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'component_article_tags', 'articleId', 'tagId');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'articleId', 'userId');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'articleId');
    }

    public function scopeFilter($query, $request)
    {
        if($request->has('search')) {
            $query->where(function($query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('content', 'like', '%'.$request->search.'%');
            });
        }

        if($request->has('statusId')) {
            $query->where('statusId', $request->input('statusId'));
        }

        if ($request->has('fromDate') && $request->has('toDate')) {   
            $query->ofDate('createdAt', $request->fromDate, $request->toDate);
        }

        if($request->has('categoryId')) {
            $query->whereHas('categories', function($query) use ($request) {
                $query->where('id', $request->input('categoryId'));
            });
        }

        return $query;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = Article::createSlug($article->title,'slug', $article->id);
            }
        });
    }

    public static function createSlug(string $title, string $column = 'slug', $id)
    {
        $article = Article::find($id);

        if ($article && $title === $article->title) {
            return $article->slug;
        }

        $slug = Str::slug($title);

        $checkSlug = Article::query()->where($column, $slug)->first();
        if ($checkSlug) {
            $title = $title ."-". uniqid();

            return self::createSlug($title, $column, $id);
        }

        return $slug;
    }
}
