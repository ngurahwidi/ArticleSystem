<?php

namespace App\Http\Controllers\Web\Article;

use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Parser\Article\ArticleParser;
use App\Parser\Comment\CommentParser;
use App\Algorithms\Article\ArticleAlgo; 
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;

class ArticleController extends Controller
{
    public function get(Request $request)
    {
       $article = Article::filter($request)->getOrPaginate($request, true);
       if($article->isEmpty()){
           errArticleGet();
       }

        return success(ArticleParser::briefs($article));
    }

    public function getById($id, Request $request)
    {
        $article = Article::with('comments')->find($id);
        if(!$article){
            errArticleGet();
         }

        return success($article);
    }

    public function create(CreateArticleRequest $request)
    {
        $algo = new ArticleAlgo();
        return $algo->create($request);
    }

    public function update($id, UpdateArticleRequest $request)
    {
        $article = Article::find($id);
        if(!$article){
           errArticleGet();
        }

        if(Auth::guard('api')->user()->id != $article->userId){
            errAccessDenied();
        }

        $algo = new ArticleAlgo($article);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $article = Article::find($id);
        if(!$article){
           errArticleGet();
        }

        if(Auth::guard('api')->user()->id != $article->userId){
            errAccessDenied();
        }

        $algo = new ArticleAlgo($article);
        return $algo->delete();
    }
}
