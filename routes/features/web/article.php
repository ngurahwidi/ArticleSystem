<?php

use App\Http\Controllers\Web\Article\ArticleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth.api'], function () {
        Route::get("", [ArticleController::class, "get"]);
        Route::post("", [ArticleController::class, "create"]);
        Route::post("{id}", [ArticleController::class, "update"]);
        Route::delete("{id}", [ArticleController::class, "delete"]);
});
       

