<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use App\Models\User\User;
use App\Services\Constant\Global\ValidationStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Component\Tag;
use App\Models\Component\Category;
use App\Parser\Article\ArticleParser;
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
        'galleries' => 'array'
    ];

    public $parserClass = ArticleParser::class;

    /** --- BOOT --- */

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = Article::createSlug($article->title,'slug', $article->id);
            }
        });
    }


    /** --- RELATIONS --- */

    public function user()
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
        return $this->belongsToMany(User::class, 'article_favorites', 'articleId', 'userId');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'articleId');
    }


    /** --- SCOPE --- */

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

        $categoryIds = $request->categoryIds;
        if($categoryIds != "") {
            $query->whereHas('categories', function($query) use ($categoryIds) {
                $query->whereIn('component_article_categories.categoryId', convert_string_to_array($categoryIds));
            });
        }

        $tagIds = $request->tagIds;
        if ($tagIds != "") {
            $query->whereHas('tags', function($query) use ($tagIds) {
                $query->whereIn('component_article_tags.tagId', convert_string_to_array($tagIds));
            });
        }

        if($request->has('sort_by_date')) {
            $query->orderBy('createdAt', $request->input('sort_by_date') === 'asc' ? 'asc' : 'desc');
        }

        if($request->has('sort_by_popular')) {
            $query->orderBy('popular', $request->input('sort_by_popular') === 'asc' ? 'asc' : 'desc');
        }

        return $query;
    }


    /** --- STATIC FUNCTIONS */

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


    /** --- FUNCTIONS --- */

    public function galleryLinks()
    {
        $result = [];
        foreach ($this->galleries as $gallery) {
            if ($link = parse_link($gallery)) {
                $result[] = $link;
            }
        }

        return $result;
    }

    public function articleFavoritedCheck()
    {

        return $this->favoritedBy()->where('userId', Auth::guard('api')->user()->id)->exists();

    }
}
