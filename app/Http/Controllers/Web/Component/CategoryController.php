<?php

namespace App\Http\Controllers\Web\Component;

use App\Http\Requests\Component\CategoryUpdateRequest;
use App\Services\Constant\User\UserRole;
use Illuminate\Http\Request;
use App\Models\Component\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Parser\Component\ComponentParser;
use App\Algorithms\Component\ComponentAlgo;
use App\Algorithms\Component\ComponentCategoryAlgo;
use App\Http\Requests\Component\CategoryCreateRequest;

class CategoryController extends Controller
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
        $categories = Category::getOrPaginate($request, true);

        return success(ComponentParser::briefs($categories));
    }

    public function getById($id)
    {
        $category = Category::find($id);

        if(!$category){
            errCategoryGet();
        }

        return success($category);
    }

    public function create(CategoryCreateRequest $request)
    {
        $algo = new ComponentCategoryAlgo();
        return $algo->create($request);
    }

    public function update($id, CategoryUpdateRequest $request)
    {
        $algo = new ComponentCategoryAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $category = Category::find($id);
        if(!$category){
            errCategoryGet();
        }

        if(Auth::user()->id != $category->createdBy){
            errAccessDenied();
        }

        $algo = new ComponentAlgo();
        return $algo->delete($category);
    }
}
