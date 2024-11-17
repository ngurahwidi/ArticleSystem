<?php

namespace App\Http\Controllers\Web\Auth;

use Illuminate\Http\Request;
use App\Algorithms\Auth\AuthAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $algo = new AuthAlgo();
        return $algo->register($request);
    }

    public function login(Request $request)
    {
        $algo = new AuthAlgo();
        return $algo->login($request);
    }

    public function logout()
    {
        $algo = new AuthAlgo();
        return $algo->logout();
    }
}
