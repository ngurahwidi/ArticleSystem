<?php

namespace App\Algorithms\Comment;

use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Models\Comment\Comment;
use Illuminate\Support\Facades\DB;
use App\Services\Misc\SaveActivity;
use Illuminate\Support\Facades\Auth;
use App\Parser\Comment\CommentParser;
use App\Services\Constant\Activity\ActivityAction;

class CommentAlgo
{

    public function __construct(public ? Article $article = null)
    {
    }
       
    public function create(Request $request)
    {
        
        try {
            DB::transaction(function () use ($request) {

                $this->validateParentId($request);

                $comment = $this->createComment($request);
                
                $this->article->setActivityPropertyAttributes(ActivityAction::CREATE)
                ->saveActivity(("Enter new comment : {$comment->comment},  [{$comment->id}]"));
            });

            return success();
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->article->setOldActivityPropertyAttributes(ActivityAction::UPDATE);
              
                $this->updateComment($request);

                $this->article->setActivityPropertyAttributes(ActivityAction::UPDATE)
                ->saveActivity("Update comment : {$request->comment},  [{$request->id}]");

            });
            return success();
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::transaction(function () use ($request){
                $this->article->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                $this->article->comments()->where('id', $request->id)->delete();

                $this->article->setActivityPropertyAttributes(ActivityAction::DELETE)
                ->saveActivity("Delete comment : [{$request->id}]");
            });
            return success();
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function createComment(Request $request)
    {
        $comment = $this->article->comments()->create([
            'userId' => Auth::guard('api')->user()->id,
            'comment' => $request->comment,
            'parentId' => $request->parentId
        ]);

        return $comment;
    }

    private function updateComment(Request $request)
    {
         $this->article->comments()->where('id', $request->id)->update([
                   'comment' => $request->comment
               ]);
    }

    private function validateParentId(Request $request)
    {
        if($request->filled('parentId')) {
            $parentComment = $this->article->comments()->where('id', $request->parentId)->first();

            if(!$parentComment) {
                errParentNotFound();
            }
        }
    }
}