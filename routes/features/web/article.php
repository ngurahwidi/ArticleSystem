<?php

use App\Http\Controllers\Web\Article\ArticleController;
use App\Http\Controllers\Web\Comment\CommentController;
use App\Http\Controllers\Web\Favorite\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth.api'], function () {
        Route::get("", [ArticleController::class, "get"]);
        Route::get("{id}", [ArticleController::class, "getById"]);
        Route::post("", [ArticleController::class, "create"]);
        Route::post("{id}/update", [ArticleController::class, "update"]);
        Route::delete("{id}", [ArticleController::class, "delete"]);

        //Favorite
        Route::post("{articleId}/favorites", [FavoriteController::class, "favorite"]);
        Route::delete("{articleId}/favorites", [FavoriteController::class, "unfavorite"]);

        //Comment
        Route::get("{articleId}/comments", [CommentController::class, "get"]);
        Route::post("{articleId}/comments", [CommentController::class, "create"]);
        Route::post("{articleId}/comments/{id}/update", [CommentController::class, "update"]);
        Route::delete("{articleId}/comments/{id}", [CommentController::class, "delete"]);
});
       

