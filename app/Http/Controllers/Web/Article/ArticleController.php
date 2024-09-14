<?php

namespace App\Http\Controllers\Web\Article;

use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Algorithms\Article\ArticleAlgo;
use App\Services\Constant\Article\StatusArticle;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Parser\Article\ArticleParser;

class ArticleController extends Controller
{
    public function get(Request $request)
    {
       $article = Article::filter($request)->getOrPaginate($request, true);

       if($article->isEmpty()){
           errArticleGet();
       }

       $parsedArticle = ArticleParser::briefs($article);

        return success($parsedArticle);
    }

    public function getById($id, Request $request)
    {
        $article = Article::find($id);

        if(!$article){
            errArticleGet();
         }

        $parsedArticle = ArticleParser::first($article);

        return success($parsedArticle);
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

        if(Auth::user()->id != $article->userId){
            errForbidden("You don't have permission to update this article");
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

        if(Auth::user()->id != $article->userId){
            errForbidden("You don't have permission to delete this article");
        }

        $algo = new ArticleAlgo($article);
        return $algo->delete();
    }
}
