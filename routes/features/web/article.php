<?php

use App\Http\Controllers\Web\Article\ArticleController;
use App\Http\Controllers\Web\Article\CommentController;
use App\Http\Controllers\Web\Article\FavoriteController;
use App\Services\Constant\User\UserRole;
use Illuminate\Support\Facades\Route;

$admin = UserRole::ADMIN_ID;
$author = UserRole::AUTHOR_ID;
$subscriber = UserRole::SUBSCRIBER_ID;

Route::group(['middleware' => 'auth.api'], function () use ($admin, $author, $subscriber) {

        Route::get("", [ArticleController::class, "get"]);
        Route::get("{id}", [ArticleController::class, "getById"]);

        Route::middleware("auth.role:$admin, $author")
        ->group(function () {
                Route::post("", [ArticleController::class, "create"]);
                Route::post("{id}/update", [ArticleController::class, "update"]);
                Route::delete("{id}", [ArticleController::class, "delete"]);
        });

        //Favorite
        Route::middleware("auth.role:$admin, $subscriber")
        ->group(function () {
                Route::post("{id}/favorites", [FavoriteController::class, "favorite"]);
                Route::delete("{id}/favorites", [FavoriteController::class, "unfavorite"]);
        });

        //Comment
        Route::get("{id}/comments", [CommentController::class, "get"]);
        Route::post("{id}/comments", [CommentController::class, "create"]);
        Route::post("{id}/comments/{commentId}/update", [CommentController::class, "update"]);
        Route::delete("{id}/comments/{commentId}", [CommentController::class, "delete"]);
});


