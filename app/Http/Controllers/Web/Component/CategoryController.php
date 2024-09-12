<?php

namespace App\Http\Controllers\Web\Component;

use Illuminate\Http\Request;
use App\Models\Component\Category;
use App\Http\Controllers\Controller;
use App\Algorithms\Component\ComponentAlgo;

class CategoryController extends Controller
{
    public function get(Request $request)
    {
        $categories = Category::getOrPaginate($request, true);
        return success($categories);
    }

    public function create(Request $request)
    {
        $algo = new ComponentAlgo();
        return $algo->createBy(Category::class, $request);
    }
}
