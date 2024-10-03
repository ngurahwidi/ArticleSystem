<?php

namespace App\Algorithms\Article;

use Illuminate\Http\Request;
use App\Models\Component\Tag;
use App\Models\Article\Article;
use App\Models\Component\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Global\StatusId;
use App\Services\Constant\Article\StatusArticle;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Global\StatusValidation;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Services\Misc\FileUpload;

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

                $this->uploadFile($request);

                $this->saveTagCategory($request);

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

                $this->uploadFile($request);

                $this->updateTagCategory($request);

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

                $this->article = $this->deleteTagCategory();

                $this->article->setActivityPropertyAttributes(ActivityAction::DELETE)
                    ->saveActivity("Delete article : {$this->article->title},  [{$this->article->id}]");
            });

            return success();

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function uploadFile($request)
    {
        $this->article->galleries = $this->saveGallery($request);
        $this->article->filepath = $this->saveFile($request);
        $this->article->save();
    }

    private function createArticle($request)
    {     
        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId,StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $categories = Category::whereIn('id', $request->categoryIds)->get();
        foreach($categories as $category) {
            if($category->statusId != StatusValidation::PUBLISH_ID) {
                errArticleCategory();
            }
        }

        $tags = Tag::whereIn('id', $request->tagIds)->get();
        foreach($tags as $tag) {
            if($tag->statusId != StatusValidation::PUBLISH_ID) {
                errArticleTag();
            }
        }

        $article = Article::create([
            'title' => $request->title,
            'slug' => Article::createSlug($request->title,'slug', $request->id),
            'userId' => $user->id,
            'description' => $request->description,
            'content' => $request->content,
            'statusId' => $request->statusId,
            'createdBy' => $user->id,
            'createdByName' => $user->username
        ]);

        

        return $article;
    }

    private function updateArticle($request)
    {
        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $categories = Category::whereIn('id', $request->categoryIds)->get();
        foreach($categories as $category) {
            if($category->statusId != StatusValidation::PUBLISH_ID) {
                errArticleCategory();
            }
        }

        $tags = Tag::whereIn('id', $request->tagIds)->get();
        foreach($tags as $tag) {
            if($tag->statusId != StatusValidation::PUBLISH_ID) {
                errArticleTag();
            }
        }

        $this->article->update([
            'title' => $request->title,
            'slug' => Article::createSlug($request->title, 'slug', $request->id),
            'userId' => $user->id,
            'description' => $request->description,
            'content' => $request->content,
            'statusId' => $request->statusId,
            'createdBy' => $user->id,
            'createdByName' => $user->username
            
        ]);   
    }

    private function saveFile($request)
    {
        if($request->hasFile('filepath')) {
            $file = $request->file('filepath');
            $title = $request->input('title');
            return FileUpload::upload($file, $title, 'uploads/article'); 
        }     
    }

    private function saveGallery(Request $request)
    {
        $imagePaths = [];

        if($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $image) {
                $title = $request->input('title');
                
                $imagePaths[] = FileUpload::upload($image, $title, 'uploads/article/gallery');
            }
        }
        return $imagePaths;
    }

    private function saveTagCategory($request)
    {
        $this->article->categories()->attach($request->input('categoryIds'));
        $this->article->tags()->attach($request->input('tagIds'));
    }

    private function updateTagCategory($request){
        $this->article->categories()->sync($request->input('categoryIds'));
        $this->article->tags()->sync($request->input('tagIds'));
    }

    private function deleteTagCategory()
    {
        $this->article->categories()->detach();
        $this->article->tags()->detach();      
    }
}