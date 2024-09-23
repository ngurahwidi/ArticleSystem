<?php

namespace App\Http\Controllers\Web\Favorite;

use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Http\Controllers\Controller;
use App\Algorithms\Favorite\FavoriteAlgo;

class FavoriteController extends Controller
{
    
    public function favorite(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
           errNotFound("Article Not Found");
        }

        $algo = new FavoriteAlgo($article);
        return $algo->create($request);
    }

    public function unfavorite(Request $request)
    {
        $article = Article::find($request->articleId);
        if(!$article){
           errNotFound("Article Not Found");
        }

        $algo = new FavoriteAlgo($article);
        return $algo->delete($request);
    }
}
