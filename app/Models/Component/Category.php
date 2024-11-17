<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\User\User;
use App\Models\Article\Article;
use App\Parser\Component\ComponentParser;
use App\Models\Component\Traits\HasActivityComponentProperty;

class Category extends BaseModel
{
    use HasActivityComponentProperty;
    protected $table = 'component_categories';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = ComponentParser::class;

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'component_article_categories', 'categoryId', 'articleId');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }

}
