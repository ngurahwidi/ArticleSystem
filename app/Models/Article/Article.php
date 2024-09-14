<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Support\Str;
use App\Models\Component\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Article\Traits\HasActivityArticleProperty;
use App\Parser\Article\ArticleParser;

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

    public $parserClass = ArticleParser::class;

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_categories', 'articleId', 'categoryId');
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

        // if($request->has('category_id')) {
        //     $query->whereHas('categories', function($query) use ($request) {
        //         $query->where('article_categories.id', $request->input('category_id'));
        //     });
        // }

        if ($request->has('fromDate') && $request->has('toDate')) {
            $query->whereDate('createdAt', '>=', $request->fromDate) 
            ->whereDate('createdAt', '<=', $request->toDate);
        }

        return $query;
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
