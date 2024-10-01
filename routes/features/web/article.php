<?php

use App\Http\Controllers\Web\Article\ArticleController;
use App\Http\Controllers\Web\Comment\CommentController;
use App\Http\Controllers\Web\Favorite\FavoriteController;
use App\Services\Constant\User\RoleUser;
use Illuminate\Support\Facades\Route;

$admin = RoleUser::ADMIN_ID;
$author = RoleUser::AUTHOR_ID;
$subscriber = RoleUser::SUBSCRIBER_ID;

Route::group(['middleware' => 'auth.api'], function () use ($admin, $author, $subscriber) {

        Route::middleware("auth.role:$admin, $author")
        ->group(function () {
                Route::get("", [ArticleController::class, "get"]);
                Route::get("{id}", [ArticleController::class, "getById"]);
                Route::post("", [ArticleController::class, "create"]);
                Route::post("{id}/update", [ArticleController::class, "update"]);
                Route::delete("{id}", [ArticleController::class, "delete"]);
        });
       
        //Favorite
        Route::middleware("auth.role:$admin, $subscriber")
        ->group(function () {
                Route::post("{articleId}/favorites", [FavoriteController::class, "favorite"]);
                Route::delete("{articleId}/favorites", [FavoriteController::class, "unfavorite"]);
        });
        
        //Comment
        Route::get("{articleId}/comments", [CommentController::class, "get"]);
        Route::post("{articleId}/comments", [CommentController::class, "create"]);
        Route::post("{articleId}/comments/{id}/update", [CommentController::class, "update"]);
        Route::delete("{articleId}/comments/{id}", [CommentController::class, "delete"]);
});
       

