<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use App\Models\User\User;
use App\Parser\Article\CommentParser;

class Comment extends BaseModel
{

    protected $table = 'article_comments';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = CommentParser::class;

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function articles()
    {
        return $this->belongsTo(Article::class, 'articleId');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parentId');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parentId');
    }

}
