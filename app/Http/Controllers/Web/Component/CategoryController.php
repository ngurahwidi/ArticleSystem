<?php

namespace App\Http\Controllers\Web\Component;

use Illuminate\Http\Request;
use App\Models\Component\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Parser\Component\ComponentParser;
use App\Algorithms\Component\ComponentAlgo;
use App\Algorithms\Component\ComponentCategoryAlgo;
use App\Http\Requests\Component\CategoryRequest;

class CategoryController extends Controller
{

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

    public function create(CategoryRequest $request)
    {
        $algo = new ComponentCategoryAlgo();
        return $algo->create($request);
    }

    public function update($id, CategoryRequest $request)
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

        if(Auth::guard('api')->user()->id != $category->createdBy){
            errAccessDenied();
        }

        $algo = new ComponentAlgo();
        return $algo->delete($category);
    }
}
