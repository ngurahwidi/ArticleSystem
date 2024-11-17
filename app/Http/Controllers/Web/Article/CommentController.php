<?php

namespace App\Http\Controllers\Web\Article;

use App\Http\Requests\Comment\CommentRequest;
use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Http\Controllers\Controller;
use App\Algorithms\Article\CommentAlgo;

class CommentController extends Controller
{

    public function get($id, Request $request)
    {
        $article = Article::find($id);
        if(!$article){
            errArticleGet();
        }

        $comments = $article->comments()->whereNull('parentId')->with(['replies'])->getOrPaginate($request, true);
        return success($comments);
    }

    public function create($id, CommentRequest $request)
    {
        $algo = new CommentAlgo((int)$id);
        return $algo->create($request);
    }

    public function update($id, $commentId, CommentRequest $request)
    {
        $algo = new CommentAlgo((int)$id, (int)$commentId);
        return $algo->update($request);
    }

    public function delete($id, $commentId, Request $request)
    {
        $algo = new CommentAlgo((int)$id, (int)$commentId);
        return $algo->delete($request);
    }
}
