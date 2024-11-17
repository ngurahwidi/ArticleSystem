<?php

namespace App\Http\Controllers\Web\Article;

use App\Http\Controllers\Controller;
use App\Algorithms\Article\FavoriteAlgo;

class FavoriteController extends Controller
{

    public function favorite($id)
    {
        $algo = new FavoriteAlgo((int)$id);
        return $algo->create();
    }

    public function unfavorite($id)
    {
        $algo = new FavoriteAlgo((int)$id);
        return $algo->delete();
    }
}
