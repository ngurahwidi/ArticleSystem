<?php

namespace App\Algorithms\Component;

use Illuminate\Http\Request;
use App\Models\Component\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\Global\StatusId;
use App\Http\Requests\Component\CategoryRequest;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Global\StatusValidation;
use App\Services\Misc\FileUpload;

class TagCategoryAlgo
{

    // public function __construct (public ? Category $category = null)
    // {
    // }

    public function create ($model, Request $request)
    {
        try {
            $component = DB::transaction(function () use ($model, $request) {
                $component = $this->createCategory($model, $request);

                $this->uploadIcon($component, $request);

                $component->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new " .$component->getTable() . ":$component->name [$component->id]");

                    return $component;
            });

            return success($component);

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function update (Model $model,Request $request)
    {
        try {
            DB::transaction(function () use ($model, $request){
                
                $model->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->updateCategory($model, $request);

                $this->uploadIcon($model, $request);

                $model->setActivityPropertyAttributes(ActivityAction::UPDATE)
                ->saveActivity("Update " . $model->getTable() . ": $model->name,[$model->id]");
            });

            return success($model->fresh());

        } catch (\Exception $exception) {
            exception($exception);    
        }
    }

    private function createCategory ($model, $request)
    {
        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $component = $model::create([
            'name' => $request->name,
            'statusId' => $request->statusId,
            'userId' => $user->id,
            'createdBy' => $user->id,
            'createdByName' => $user->username
        ]);

        return $component;
    }

    private function updateCategory ($model, $request)
    {

        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $model->update([
            'name' => $request->name,
            'statusId' => $request->statusId,
            'userId' => $user->id,
        ]);
    }

    private function uploadIcon($model, $request)
    {
        $model->icon = $this->saveIcon($request);
        $model->save();
    }

    private function saveIcon (Request $request)
    {
        $icon = $request->file('icon');
        $name = $request->input('name');
        return FileUpload::upload($icon, $name, 'uploads/component');
    }
}