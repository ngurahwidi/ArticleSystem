<?php

namespace App\Algorithms\Article;

use App\Models\Article\Article;
use App\Models\Component\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Global\StatusId;
use App\Services\Constant\Article\StatusArticle;
use App\Services\Constant\Activity\ActivityAction;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Models\Component\Tag;
use App\Services\Constant\Global\StatusValidation;

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

                $this->article->categories()->detach();

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

        $galleries = $this->saveGallery($request);

        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId,StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $categories = Category::whereIn('id', $request->categoryIds)->get();
        foreach($categories as $category) {
            if($category->statusId != StatusValidation::PUBLISH_ID) {
                errArticleValidStatus('You are not authorized to create an article, article category must be publish');
            }
        }

        $tags = Tag::whereIn('id', $request->tagIds)->get();
        foreach($tags as $tag) {
            if($tag->statusId != StatusValidation::PUBLISH_ID) {
                errArticleValidStatus('You are not authorized to create an article, article tag must be publish');
            }
        }

        $article = Article::create([
            'title' => $request->title,
            'slug' => Article::createSlug($request->title,'slug', $request->id),
            'userId' => $user->id,
            'description' => $request->description,
            'content' => $request->content,
            'filepath' => $filePath,
            'galleries' => $galleries,
            'statusId' => $request->statusId,
            'createdBy' => $user->id,
            'createdByName' => $user->username
        ]);

        $article->categories()->attach($request->input('categoryIds'));
        $article->tags()->attach($request->input('tagIds'));

        return $article;
    }

    private function updateArticle($request)
    {
        $filePath = $this->saveFile($request);

        $gallery = $this->saveGallery($request);

        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $categories = Category::whereIn('id', $request->categoryIds)->get();
        foreach($categories as $category) {
            if($category->statusId != StatusValidation::PUBLISH_ID) {
                errArticleValidStatus('You are not authorized to create an article, article category must be publish');
            }
        }

        $tags = Tag::whereIn('id', $request->tagIds)->get();
        foreach($tags as $tag) {
            if($tag->statusId != StatusValidation::PUBLISH_ID) {
                errArticleValidStatus('You are not authorized to create an article, article tag must be publish');
            }
        }

        $this->article->categories()->sync($request->input('categoryIds'));
        $this->article->tags()->sync($request->input('tagIds'));

        $this->article->update([
            'title' => $request->title,
            'slug' => Article::createSlug($request->title, 'slug', $request->id),
            'userId' => $user->id,
            'description' => $request->description,
            'content' => $request->content,
            'filepath' => $filePath,
            'gallery' => $gallery,
            'statusId' => $request->statusId,
            'createdBy' => $user->id,
            'createdByName' => $user->username
            
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

    private function saveGallery($request)
    {
        $imagePaths = [];

        if($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $image) {
                $title = $request->input('title');
                $sanitizedTitle = str_replace(' ', '_', $title);
                $lowerTitle = strtolower($sanitizedTitle);
                $extension = $image->getClientOriginalExtension();
                $filename = $lowerTitle.'.'.$extension;
                $imagePath = $image->storeAs('uploads/article', $filename, 'public');
                $imagePaths[] = $imagePath;
            }
        }
        return $imagePaths;
    }
}