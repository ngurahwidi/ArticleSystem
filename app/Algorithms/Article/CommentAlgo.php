<?php

namespace App\Algorithms\Article;

use Illuminate\Http\Request;
use App\Models\Article\Article;
use App\Models\Article\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;

class CommentAlgo
{

    protected $user;

    public function __construct(public Article|int|null $article = null, protected Comment|int|null $comment = null)
    {

        $this->user = Auth::user();

        if(is_int($this->article)) {
            $this->article = Article::find($this->article);
            if(!$this->article) {
                errArticleGet();
            }
        }

        if(is_int($this->comment)) {
            $this->comment = Comment::find($this->comment);
            if(!$this->comment) {
                errCommentGet();
            }

            if($this->user->id != $this->comment->userId){
                errAccessDenied();
            }
        }
    }

    public function create(Request $request)
    {

        try {

            DB::transaction(function () use ($request) {

                $this->validateCommentParentId($request);

                $comment =$this->createComment($request);

               activity()->setAction(ActivityAction::CREATE)
                    ->setType(ActivityType::ARTICLE)
                    ->setSubType("comment")
                    ->setReference($this->article)
                    ->log("Create comment : ($comment->comment), [$comment->id]");
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

                $this->updateComment($request);

               activity()->setAction(ActivityAction::UPDATE)
                    ->setType(ActivityType::ARTICLE)
                    ->setSubType("comment")
                    ->setReference($this->article)
                    ->log("Update comment : ($request->comment), [$request->id]");

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

                $this->article->comments()->where('id', $request->commentId)->delete();

                activity()->setAction(ActivityAction::DELETE)
                    ->setType(ActivityType::ARTICLE)
                    ->setSubType("comment")
                    ->setReference($this->article)
                    ->log("Delete comment : [$request->id]");
            });
            return success();
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function createComment(Request $request)
    {

        $comment = $this->article->comments()->create([
            'userId' => $this->user->id,
            'comment' => $request->comment,
            'parentId' => $request->parentId
        ]);
        if(!$comment) {
            errCommentSave();
        }

        return $comment;
    }

    private function updateComment(Request $request)
    {
         $comment = $this->article->comments()->where('id', $request->commentId)->update([
             'comment' => $request->comment
         ]);
        if(!$comment) {
            errCommentUpdate();
        }
    }

    private function validateCommentParentId(Request $request)
    {
        if($request->filled('parentId')) {
            $parentComment = $this->article->comments()->where('id', $request->parentId)->first();
            if(!$parentComment) {
                errCommentParentGet();
            }
        }
    }
}
