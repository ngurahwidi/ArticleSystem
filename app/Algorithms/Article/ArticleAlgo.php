<?php

namespace App\Algorithms\Article;

use App\Models\Article\Article;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\Article\StatusArticle;
use App\Services\Constant\Activity\ActivityAction;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;

class ArticleAlgo
{
    public function __construct(public ? Article $article = null) 
    {
    }

    public function create(CreateArticleRequest $request)
    {
        try {
            
            DB::transaction(function () use ($request) {
                
                $this->article = $this->createArticle($request);

                $this->article->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new article : {$this->article->title},  [{$this->article->id}]");
            });

            return success($this->article);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function update(UpdateArticleRequest $request)
    {
        try {
            
            DB::transaction(function () use ($request) {

                $this->article->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->updateArticle($request);

                $this->article->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Update article : {$this->article->title},  [{$this->article->id}]");
            });

            return success($this->article->fresh());

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete()
    {
        try {
            
            DB::transaction(function () {
                
                $this->article->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                $this->article->delete();

                $this->article->setActivityPropertyAttributes(ActivityAction::DELETE)
                    ->saveActivity("Delete article : {$this->article->title},  [{$this->article->id}]");
            });

            return success();

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function createArticle($request)
    {
        $filePath = $this->saveFile($request);

        if ($request->statusId != StatusArticle::DRAFT_ID && $request->statusId != StatusArticle::PUBLISH_ID) {
            errStatusId();
        }

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'statusId' => $request->statusId,
            'filepath' => $filePath
        ]);

        return $article;
    }

    private function updateArticle($request)
    {
        if($request->statusId != StatusArticle::DRAFT_ID && $request->statusId != StatusArticle::PUBLISH_ID) {
            errStatusId();
        }
        $this->article->update([
            'title' => $request->title,
            'content' => $request->content,
            'statusId' => $request->statusId,
            
        ]);
    }

    private function saveFile($request)
    {
        $file = $request->file('filepath');
        $title = $request->input('title');
        $sanitizedTitle = str_replace(' ', '_', $title);
        $lowerTitle = strtolower($sanitizedTitle);
        $extension = $file->getClientOriginalExtension();
        $filename = $lowerTitle.'.'.$extension;
        $filePath = $file->storeAs('uploads/article', $filename, 'public');  
        return $filePath;
    }
}