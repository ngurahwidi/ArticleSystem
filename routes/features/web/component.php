<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Component\TagController;
use App\Http\Controllers\Web\Component\CategoryController;
use App\Http\Controllers\Web\Component\ComponentStaticController;


Route::prefix("components")
->middleware("auth.api")
->group(function () {


    Route::prefix("categories")
    ->group(function () {

        Route::get("", [CategoryController::class, "get"]);
        Route::post("", [CategoryController::class, "create"]);
        Route::get("{id}", [CategoryController::class, "getById"]);
        Route::delete("{id}", [CategoryController::class, "delete"]);
        Route::post("{id}/update", [CategoryController::class, "update"]);
    });

    Route::prefix("tags")
    ->group(function () {

        Route::get("", [TagController::class, "get"]);
        Route::post("", [TagController::class, "create"]);
        Route::get("{id}", [TagController::class, "getById"]);
        Route::delete("{id}", [TagController::class, "delete"]);
        Route::post("{id}/update", [TagController::class, "update"]);
    });
});

Route::get("status", [ComponentStaticController::class, "validationStatus"]);
