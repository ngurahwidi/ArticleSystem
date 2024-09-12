<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\User\User;
use App\Models\Article\Article;

class Category extends BaseModel
{
    protected $table = 'categories';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_categories', 'categoryId', 'articleId');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }

}
