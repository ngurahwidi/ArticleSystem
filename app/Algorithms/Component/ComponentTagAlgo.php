<?php

namespace App\Algorithms\Component;

use App\Services\Constant\Global\Path;
use Illuminate\Http\Request;
use App\Models\Component\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Global\ValidationStatus;
use App\Services\Misc\FileUpload;

class ComponentTagAlgo
{

    public function __construct (public Tag|int|null $tag = null)
    {
        if(is_int($this->tag)) {
            $this->tag = Tag::find($this->tag);
            if(!$this->tag) {
                errTagGet();
            }

            if(Auth::guard('api')->user()->id != $this->tag->createdBy) {
                errAccessDenied();
            }
        }
    }

    public function create (Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->tag = $this->createTag($request);

                $this->uploadIcon($request);

                $this->tag->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new tag : {$this->tag->name} [{$this->tag->id}]");
            });

            return success($this->tag);

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function update (Request $request)
    {
        try {
            DB::transaction(function () use ($request){

                $this->tag->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->updateTag($request);

                $this->uploadIcon($request);

                $this->tag->setActivityPropertyAttributes(ActivityAction::UPDATE)
                ->saveActivity("Update tag : {$this->tag->name} [{$this->tag->id}]");
            });

            return success($this->tag->fresh());

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function createTag ($request)
    {
        $user = Auth::guard('api')->user();

        if (!in_array($request->statusId, ValidationStatus::VALIDATION_STATUS)) {
            errValidationStatus();
        }

        $tag = Tag::create([
            'name' => $request->name,
            'statusId' => $request->statusId,
            'createdBy' => $user->id,
            'createdByName' => $user->username
        ]);
        if(!$tag) {
            errTagSave();
        }

        return $tag;
    }

    private function updateTag ($request)
    {

        if (!in_array($request->statusId, ValidationStatus::VALIDATION_STATUS)) {
            errValidationStatus();
        }

        $tag = $this->tag->update([
            'name' => $request->name,
            'statusId' => $request->statusId,
        ]);
        if(!$tag) {
            errTagUpdate();
        }
    }

    private function uploadIcon (Request $request)
    {
        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {

            $icon = $request->file('icon');
            $filePath = FileUpload::upload($icon, $request->name, Path::COMPONENT_TAG);
        }

        $this->tag->icon = $filePath;
        $this->tag->save();
        return $this->tag?->icon ?: null;
    }
}
