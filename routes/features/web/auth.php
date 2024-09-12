<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\AuthController;


Route::prefix("auths")
 ->group(function () {

        Route::post("/register", [AuthController::class, "register"]);
        Route::post("/login", [AuthController::class, "login"]);
        Route::middleware("auth.api")->post("/logout", [AuthController::class, "logout"]);

    });