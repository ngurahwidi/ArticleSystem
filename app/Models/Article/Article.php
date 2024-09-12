<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Article\Traits\HasActivityArticleProperty;
use App\Models\Component\Category;

class Article extends BaseModel
{
    use HasActivityArticleProperty;
    
    protected $table = 'articles';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_categories', 'articleId', 'categoryId');
    }

}
