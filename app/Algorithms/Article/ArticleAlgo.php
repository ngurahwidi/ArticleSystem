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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleAlgo
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

            if($this->user->id != $this->article->userId){
                errAccessDenied();
            }
        }
    }

    public function create(CreateArticleRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->findArticleCategoryStatus($request);

                $this->findArticleTagStatus($request);

                $this->article = $this->createArticle($request);

                $this->saveFeaturedImage($request);

                $this->uploadGallery($request);

                $this->saveTagCategory($request);

                $this->article->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new article : {$this->article->title},  [{$this->article->id}]");
            });

            return success($this->article);
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function update(UpdateArticleRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->article->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->findArticleCategoryStatus($request);

                $this->findArticleTagStatus($request);

                $this->updateArticle($request);

                $this->saveFeaturedImage($request);

                $this->uploadGallery($request);

                $this->updateTagCategory($request);

                $this->article->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Update article : {$this->article->title},  [{$this->article->id}]");
            });

            return success($this->article->fresh());

        } catch (Exception $exception) {
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

        } catch (Exception $exception) {
            exception($exception);
        }
    }

    private function createArticle($request)
    {

        if (!in_array($request->statusId,ValidationStatus::VALIDATION_STATUS)) {
            errValidationStatus();
        }

        $article = Article::create([
            'title' => $request->title,
            'slug' => Article::createSlug($request->title,'slug', $request->id),
            'userId' => $this->user->id,
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

        $article = $this->article->update($request->all());
        if(!$article) {
            errArticleUpdate();
        }
    }

    private function saveFeaturedImage($request)
    {

        if ($request->deleteFeaturedImage) {
            $oldFeaturedImage = $this->article->featuredImage;
            if (file_exists(Path::STORAGE_PUBLIC_PATH($oldFeaturedImage))) {
                unlink(Path::STORAGE_PUBLIC_PATH($oldFeaturedImage));
            }

            $this->article->featuredImage = null;
        } else {

            if($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {

                $file = $request->file('featuredImage');
                $filePath = FileUpload::upload($file, $request->title, Path::ARTICLE);
                $this->article->featuredImage = $filePath;
            }
        }

        $this->article->save();
    }

    private function uploadGallery(Request $request)
    {
        $imagePaths = $this->article->galleries ?: [];

        foreach ($request->deletedGalleries ?: [] as $deletedGallery) {
            foreach ($imagePaths as $key => $imagePath) {
                if ($deletedGallery != $imagePath) {
                    continue;
                }

                if (file_exists(Path::STORAGE_PUBLIC_PATH($deletedGallery))) {
                    unlink(Path::STORAGE_PUBLIC_PATH($deletedGallery));
                }

                unset($imagePaths[$key]);
            }
        }

        $imagePaths = array_values($imagePaths);
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $gallery) {
                $imagePaths[] = FileUpload::upload($gallery, $request->title, Path::ARTICLE_GALLERY);
                $this->article->galleries = $imagePaths;
            }
        }

        $this->article->save();
    }

    private function saveTagCategory($request)
    {
        $this->article->categories()->attach($request->input('categoryIds'));
        $this->article->tags()->attach($request->input('tagIds'));
    }

    private function updateTagCategory($request)
    {
        $this->article->categories()->sync($request->input('categoryIds'));
        $this->article->tags()->sync($request->input('tagIds'));
    }

    private function deleteTagCategory()
    {
        $this->article->categories()->detach();
        $this->article->tags()->detach();
    }

    private function findArticleCategoryStatus($request)
    {

        $categories = Category::whereIn('id', $request->categoryIds)->get();
        foreach($categories as $category) {
            if($category->statusId != ValidationStatus::PUBLISH_ID) {
                errArticleCategory();
            }
        }

        return $categories;
    }

    private function findArticleTagStatus($request)
    {

        $tags = Tag::whereIn('id', $request->tagIds)->get();
        foreach($tags as $tag) {
            if ($tag->statusId != ValidationStatus::PUBLISH_ID) {
                errArticleTag();
            }
        }
    }
}
