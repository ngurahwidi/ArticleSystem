<?php

namespace App\Http\Controllers\Web\Component;

use App\Algorithms\Component\ComponentAlgo;
use App\Http\Requests\Component\TagUpdateRequest;
use App\Services\Constant\User\UserRole;
use Illuminate\Http\Request;
use App\Models\Component\Tag;
use App\Http\Controllers\Controller;
use App\Parser\Component\ComponentParser;
use App\Algorithms\Component\ComponentTagAlgo;
use App\Http\Requests\Component\TagCreateRequest;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{

    public function __construct()
    {
        $user = Auth::user();

        $this->middleware(function ($request, $next) use ($user) {
           if (!has_role([UserRole::ADMIN_ID, UserRole::AUTHOR_ID], $user)) {
               errAccessDenied();
           }

           return $next($request);
        });
    }
    public function get(Request $request)
    {
        $tags = Tag::getOrPaginate($request, true);

        return success(ComponentParser::briefs($tags));
    }

    public function getById($id)
    {
        $tags = Tag::find($id);
        if(!$tags){
            errTagGet();
        }

        return success($tags);
    }

    public function create(TagCreateRequest $request)
    {
        $algo = new ComponentTagAlgo();
        return $algo->create($request);
    }

    public function update($id, TagUpdateRequest $request)
    {
        $algo = new ComponentTagAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            errTagGet();
        }

        if(Auth::user()->id != $tag->createdBy){
            errAccessDenied();
        }

        $algo = new ComponentAlgo();
        return $algo->delete($tag);
    }
}
