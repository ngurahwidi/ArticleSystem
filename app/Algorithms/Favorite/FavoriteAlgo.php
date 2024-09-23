<?php

namespace App\Algorithms\Favorite;

use App\Models\Article\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Activity\ActivityAction;
use Exception;

class FavoriteAlgo
{
    public function __construct(public ? Article $article = null)
    {      
    }

    public function create()
    {
        try {

            DB::transaction(function () {

                if ($this->article->favoritedBy()->where('userId', Auth::guard('api')->user()->id)->exists()) {
                    errArticleFavorite("Article Already Favorited");
                }

                $this->article->favoritedBy()->attach(Auth::guard('api')->user()->id);

                $this->article->increment('popular');
                
                $this->article->setActivityPropertyAttributes(ActivityAction::CREATE)
                ->saveActivity("Enter new favorite : {$this->article->title},  [{$this->article->id}]");
            });
            return success();
        } catch (\Exception $exception) {
           exception($exception);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                if (!$this->article->favoritedBy()->where('userId', Auth::guard('api')->user()->id)->exists()) {
                    errArticleFavorite("Article not in Favorites");
                }

                $this->article->favoritedBy()->detach(Auth::guard('api')->user()->id);

                $this->article->decrement('popular');
                
                $this->article->setActivityPropertyAttributes(ActivityAction::DELETE)
                ->saveActivity("Delete favorite : {$this->article->title},  [{$this->article->id}]");
            });

            return success();
        } catch (\Exception $exception) {
           exception($exception);
        }
    }
}