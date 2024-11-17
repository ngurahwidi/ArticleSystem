<?php

namespace App\Http\Controllers\Web\Article;

use App\Services\Constant\User\UserRole;
use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Http\Controllers\Controller;
use App\Parser\Article\ArticleParser;
use App\Algorithms\Article\ArticleAlgo;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{

    public function __construct()
    {
        $user = Auth::user();

        $this->middleware(function ($request, $next) use ($user) {
            if (!has_role([UserRole::ADMIN_ID, UserRole::AUTHOR_ID], $user)) {
                errAccessDenied();
            }
            return $next($request);
        })->except(["get", "getById"]);
    }
    public function get(Request $request)
    {

       $article = Article::filter($request)->getOrPaginate($request, true);
       if($article->isEmpty()){
           errArticleGet();
       }

        return success(ArticleParser::briefs($article));
    }

    public function getById($id)
    {

        $article = Article::find($id);
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
        $algo = new ArticleAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new ArticleAlgo((int)$id);
        return $algo->delete();
    }
}
