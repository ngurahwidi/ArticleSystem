<?php

namespace App\Algorithms\Article;

use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Models\Article\Article;
use App\Models\Component\Category;
use App\Models\Component\Tag;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Global\Path;
use App\Services\Constant\Global\ValidationStatus;
use App\Services\Misc\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleAlgo
{
    public function __construct(public Article|int|null $article = null)
    {
        if(is_int($this->article)) {
            $this->article = Article::find($this->article);
            if(!$this->article) {
                errArticleGet();
            }

            if(Auth::guard('api')->user()->id != $this->article->userId){
                errAccessDenied();
            }
        }
    }

    public function create(CreateArticleRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->article = $this->createArticle($request);

                $this->uploadFeaturedImage($request);

                $this->uploadGallery($request);

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

                $this->uploadFeaturedImage($request);

                $this->uploadGallery($request);

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

                $this->deleteTagCategory();

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
        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId,ValidationStatus::VALIDATION_STATUS)) {
            errValidationStatus();
        }

        $categories = Category::whereIn('id', $request->categoryIds)->get();
        foreach($categories as $category) {
            if($category->statusId != ValidationStatus::PUBLISH_ID) {
                errArticleCategory();
            }
        }

        $tags = Tag::whereIn('id', $request->tagIds)->get();
        foreach($tags as $tag) {
            if($tag->statusId != ValidationStatus::PUBLISH_ID) {
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
        ]);
        if(!$article) {
            errArticleSave();
        }

        return $article;
    }

    private function updateArticle($request)
    {

        if (!in_array($request->statusId, ValidationStatus::VALIDATION_STATUS)) {
            errValidationStatus();
        }

        $categories = Category::whereIn('id', $request->categoryIds)->get();
        foreach($categories as $category) {
            if($category->statusId != ValidationStatus::PUBLISH_ID) {
                errArticleCategory();
            }
        }

        $tags = Tag::whereIn('id', $request->tagIds)->get();
        foreach($tags as $tag) {
            if($tag->statusId != ValidationStatus::PUBLISH_ID) {
                errArticleTag();
            }
        }

        $article = $this->article->update([
            'title' => $request->title,
            'slug' => Article::createSlug($request->title, 'slug', $request->id),
            'description' => $request->description,
            'content' => $request->content,
            'statusId' => $request->statusId,

        ]);
        if(!$article) {
            errArticleUpdate();
        }
    }

    private function uploadFeaturedImage($request)
    {
        if($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {

            $file = $request->file('featuredImage');
            $filePath = FileUpload::upload($file, $request->title, Path::ARTICLE);
        }

        $this->article->featuredImage = $filePath;
        $this->article->save();
        return $this->article?->featuredImage ?: null;
    }

    private function uploadGallery(Request $request)
    {
        $imagePaths = [];

        if($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $image) {

                $imagePaths[] = FileUpload::upload($image, $request->title, PATH::ARTICLE_GALLERY);
            }
        }

        $this->article->galleries = $imagePaths;
        $this->article->save();
        return $this->article?->galleries ?: null;
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
