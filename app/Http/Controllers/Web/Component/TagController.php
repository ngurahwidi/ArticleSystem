<?php

namespace App\Http\Controllers\Web\Component;

use App\Algorithms\Component\ComponentAlgo;
use Illuminate\Http\Request;
use App\Models\Component\Tag;
use App\Http\Controllers\Controller;
use App\Parser\Component\ComponentParser;
use App\Algorithms\Component\TagCategoryAlgo;
use App\Http\Requests\Component\TagRequest;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function get(Request $request)
    {
        $tags = Tag::getOrPaginate($request, true);

        return success(ComponentParser::briefs($tags));
    }

    public function getById($id, Request $request)
    {
        $tags = Tag::find($id);
        if(!$tags){
            errNotFound("Tag Not Found");
        }

        return success($tags);
    }

    public function create(TagRequest $request)
    {
        $algo = new TagCategoryAlgo();
        return $algo->create(Tag::class, $request);
    }

    public function update($id, TagRequest $request)
    {
        $tag = Tag::find($id);
        if(!$tag){
            errNotFound("Tag Not Found");
        }

        if(Auth::guard('api')->user()->id != $tag->userId){
            errForbidden("You don't have permission to update this tag");
        }

        $algo = new TagCategoryAlgo();
        return $algo->update($tag, $request);
    }

    public function delete($id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            errNotFound("Tag Not Found");
        }

        if(Auth::guard('api')->user()->id != $tag->userId){
            errForbidden("You don't have permission to delete this tag");
        }

        $algo = new ComponentAlgo();
        return $algo->delete($tag);
    }
}
