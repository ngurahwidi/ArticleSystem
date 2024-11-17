<?php

use App\Http\Controllers\Web\Article\ArticleController;
use App\Http\Controllers\Web\Article\CommentController;
use App\Http\Controllers\Web\Article\FavoriteController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth.api'], function () {

        //Article
        Route::get("", [ArticleController::class, "get"]);
        Route::post("", [ArticleController::class, "create"]);
        Route::get("{id}", [ArticleController::class, "getById"]);
        Route::delete("{id}", [ArticleController::class, "delete"]);
        Route::post("{id}/update", [ArticleController::class, "update"]);

        //Favorite
        Route::post("{id}/favorites", [FavoriteController::class, "favorite"]);
        Route::delete("{id}/favorites", [FavoriteController::class, "unfavorite"]);

        //Comment
        Route::get("{id}/comments", [CommentController::class, "get"]);
        Route::post("{id}/comments", [CommentController::class, "create"]);
        Route::delete("{id}/comments/{commentId}", [CommentController::class, "delete"]);
        Route::post("{id}/comments/{commentId}/update", [CommentController::class, "update"]);
});


