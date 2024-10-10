<?php

namespace App\Algorithms\Component;

use App\Services\Constant\Global\Path;
use Illuminate\Http\Request;
use App\Models\Component\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Global\ValidationStatus;
use App\Services\Misc\FileUpload;

class ComponentCategoryAlgo
{

    public function __construct (public Category|int|null $category = null)
    {
        if(is_int($this->category)) {
            $this->category = Category::find($this->category);
            if(!$this->category) {
                errCategoryGet();
            }

            if(Auth::guard('api')->user()->id != $this->category->createdBy) {
                errAccessDenied();
            }
        }
    }

    public function create (Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->category = $this->createCategory($request);

                $this->uploadIcon($request);

                $this->category->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new category : {$this->category->name} [{$this->category->id}]");
            });

            return success($this->category);

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function update (Request $request)
    {
        try {
            DB::transaction(function () use ($request){

                $this->category->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->updateCategory($request);

                $this->uploadIcon($request);

                $this->category->setActivityPropertyAttributes(ActivityAction::UPDATE)
                ->saveActivity("Update category : {$this->category->name} [{$this->category->id}]");
            });

            return success($this->category->fresh());

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function createCategory ($request)
    {
        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, ValidationStatus::VALIDATION_STATUS)) {
            errValidationStatus();
        }

        $category = Category::create([
            'name' => $request->name,
            'statusId' => $request->statusId,
            'createdBy' => $user->id,
            'createdByName' => $user->username
        ]);
        if(!$category) {
            errCategorySave();
        }

        return $category;
    }

    private function updateCategory ($request)
    {

        if (!in_array($request->statusId, ValidationStatus::VALIDATION_STATUS)) {
            errValidationStatus();
        }

        $category = $this->category->update([
            'name' => $request->name,
            'statusId' => $request->statusId,
        ]);
        if(!$category) {
            errCategoryUpdate();
        }
    }

    private function uploadIcon (Request $request)
    {
        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {

            $icon = $request->file('icon');
            $filePath = FileUpload::upload($icon, $request->name, Path::COMPONENT_CATEGORY);
        }


        $this->category->icon = $filePath;
        $this->category->save();
        return $this->category?->icon ?: null;
    }
}
