<?php

namespace App\Http\Controllers\Web\Component;

use Illuminate\Http\Request;
use App\Models\Component\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Parser\Component\ComponentParser;
use App\Algorithms\Component\ComponentAlgo;
use App\Algorithms\Component\TagCategoryAlgo;
use App\Http\Requests\Component\CategoryRequest;

class CategoryController extends Controller
{
    public function get(Request $request)
    {
        $categories = Category::getOrPaginate($request, true);

        return success(ComponentParser::briefs($categories));
    }

    public function getById($id, Request $request)
    {
        $category = Category::find($id);

        if(!$category){
            errNotFound("Category Not Found");
        }

        return success($category);
    }

    public function create(CategoryRequest $request)
    {
        $algo = new TagCategoryAlgo();
        return $algo->create(Category::class, $request);
    }

    public function update($id, CategoryRequest $request)
    {
        $category = Category::find($id);

        if(!$category){
            errNotFound("Category Not Found");
        }

        if(Auth::guard('api')->user()->id != $category->userId){
            errForbidden("You don't have permission to update this category");
        }

        $algo = new TagCategoryAlgo();
        return $algo->update($category, $request);
    }

    public function delete($id)
    {
        $category = Category::find($id);

        if(!$category){
            errNotFound("Category Not Found");
        }

        if(Auth::guard('api')->user()->id != $category->userId){
            errForbidden("You don't have permission to delete this category");
        }

        $algo = new ComponentAlgo();
        return $algo->delete($category);
    }
}
