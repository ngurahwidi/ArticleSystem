<?php

namespace App\Algorithms\Article;

use App\Models\Article\Article;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Activity\ActivityAction;

class FavoriteAlgo
{
    public function __construct(public Article|int|null $article = null)
    {
        if(is_int($this->article)) {
            $this->article = Article::find($this->article);
            if(!$this->article) {
                errArticleGet();
            }
        }
    }

    public function create()
    {
        try {

            DB::transaction(function () {

                if ($this->article->favoritedBy()->where('userId', Auth::guard('api')->user()->id)->exists()) {
                    errArticleFavorite();
                }

                $this->article->favoritedBy()->attach(Auth::guard('api')->user()->id);

                $this->article->increment('popular');

                $this->article->setActivityPropertyAttributes(ActivityAction::CREATE)
                ->saveActivity("Enter new favorite : {$this->article->title},  [{$this->article->id}]");
            });
            return success();
        } catch (Exception $exception) {
           exception($exception);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                $this->article->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                if (!$this->article->favoritedBy()->where('userId', Auth::guard('api')->user()->id)->exists()) {
                    errArticleUnFavorite();
                }

                $this->article->favoritedBy()->detach(Auth::guard('api')->user()->id);

                $this->article->decrement('popular');

                $this->article->setActivityPropertyAttributes(ActivityAction::DELETE)
                ->saveActivity("Delete favorite : {$this->article->title},  [{$this->article->id}]");
            });

            return success();
        } catch (Exception $exception) {
           exception($exception);
        }
    }
}
