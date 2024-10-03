<?php

namespace App\Http\Controllers\Web\Comment;

use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Http\Controllers\Controller;
use App\Algorithms\Comment\CommentAlgo;
use App\Models\Comment\Comment;
use App\Parser\Comment\CommentParser;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function get(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
            errArticleGet();
        }

        $comments = $article->comments;

        return success(CommentParser::briefs($comments));
    }
    
    public function create(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
           errArticleGet();
        }
        
        $algo = new CommentAlgo($article);
        return $algo->create($request);
    }

    public function update(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
           errArticleGet();
        }

        $comment = Comment::find($request->id);
        if(!$comment){
            errCommentGet();
        }

        $user = Auth::guard('api')->user();
        if($user->id != $comment->userId){
            errAccessDenied();
        }

        $algo = new CommentAlgo($article);
        return $algo->update($request);
    }

    public function delete(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
            errArticleGet();
        }

        $comment = Comment::find($request->id);
        if(!$comment){
            errCommentGet();
        }

        $user = Auth::guard('api')->user();
        if($user->id != $comment->userId){
            errAccessDenied();
        }

        $algo = new CommentAlgo($article);
        return $algo->delete($request);
    }
}
