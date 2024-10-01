<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Component\TagController;
use App\Http\Controllers\Web\Component\CategoryController;
use App\Services\Constant\User\RoleUser;

$admin = RoleUser::ADMIN_ID;
$author = RoleUser::AUTHOR_ID;

Route::prefix("components")
->middleware("auth.api")
->group(function () use ($admin, $author) {
   
    
    Route::prefix("categories")
    ->middleware("auth.role:$admin, $author")
    ->group(function () {

        Route::get("", [CategoryController::class, "get"]);
        Route::get("{id}", [CategoryController::class, "getById"]);
        Route::post("", [CategoryController::class, "create"]);
        Route::post("{id}/update", [CategoryController::class, "update"]);
        Route::delete("{id}", [CategoryController::class, "delete"]);
    });

    Route::prefix("tags")
    ->middleware("auth.role:$admin, $author")
    ->group(function () {

        Route::get("", [TagController::class, "get"]);
        Route::get("{id}", [TagController::class, "getById"]);
        Route::post("", [TagController::class, "create"]);
        Route::post("{id}/update", [TagController::class, "update"]);
        Route::delete("{id}", [TagController::class, "delete"]);
    });
});