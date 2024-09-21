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
        $icon = $this->saveIcon($request);

        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $component = $model::create([
            'name' => $request->name,
            'icon' => $icon,
            'statusId' => $request->statusId,
            'userId' => $user->id,
            'createdBy' => $user->id,
            'createdByName' => $user->username
        ]);

        return $component;
    }

    private function updateCategory ($model, $request)
    {
        $icon = $this->saveIcon($request);

        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, StatusValidation::VALIDATION_STATUS)) {
            errStatusNotFound();
        }

        $model->update([
            'name' => $request->name,
            'icon' => $icon,
            'statusId' => $request->statusId,
            'userId' => $user->id,
        ]);
    }

    private function saveIcon (Request $request)
    {
        $icon = $request->file('icon');
        $name = $request->input('name');
        $sanitizedName = str_replace(' ', '_', $name);
        $lowerName = strtolower($sanitizedName);
        $extension = $icon->getClientOriginalExtension();
        $iconName = $lowerName.'.'.$extension;

        $iconPath = $icon->storeAs('uploads/component', $iconName, 'public'); 
        return $iconPath;
    }
}