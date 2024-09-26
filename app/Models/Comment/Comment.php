<?php

namespace App\Models\Comment;

use App\Models\Article\Article;
use App\Models\BaseModel;
use App\Models\User\User;

class Comment extends BaseModel
{
    protected $table = 'comments';
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
