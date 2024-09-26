<?php

namespace App\Http\Controllers\Web\Comment;

use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Http\Controllers\Controller;
use App\Algorithms\Comment\CommentAlgo;
use App\Models\Comment\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function get(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
            errNotFound("Article Not Found");
        }
        
        $algo = new CommentAlgo($article);
        return $algo->get($request);
    }
    
    public function create(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
           errNotFound("Article Not Found");
        }
        
        $algo = new CommentAlgo($article);
        return $algo->create($request);
    }

    public function update(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
           errNotFound("Article Not Found");
        }

        $comment = Comment::find($request->id);
        if(!$comment){
            errNotFound("Comment Not Found");
        }

        $user = Auth::guard('api')->user();
        if($user->id != $comment->userId){
            errForbidden("You don't have permission to update this comment");
        }

        $algo = new CommentAlgo($article);
        return $algo->update($request);
    }

    public function delete(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
            errNotFound("Article Not Found");
        }

        $comment = Comment::find($request->id);
        if(!$comment){
            errNotFound("Comment Not Found");
        }

        $user = Auth::guard('api')->user();
        if($user->id != $comment->userId){
            errForbidden("You don't have permission to delete this comment");
        }

        $algo = new CommentAlgo($article);
        return $algo->delete($request);
    }
}
