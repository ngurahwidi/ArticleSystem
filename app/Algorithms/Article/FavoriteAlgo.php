<?php

namespace App\Algorithms\Article;

use App\Models\Article\Article;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Activity\ActivityAction;

class FavoriteAlgo
{

    protected $user;

    public function __construct(public Article|int|null $article = null)
    {

        $this->user = Auth::user();

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

                if ($this->article->articleFavoritedCheck()){
                    errArticleFavorite();
                }

                $this->article->favoritedBy()->attach($this->user->id);

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

                if (!$this->article->articleFavoritedCheck()) {
                    errArticleUnFavorite();
                }

                $this->article->favoritedBy()->detach($this->user->id);

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
