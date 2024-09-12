<?php

namespace App\Http\Controllers\Web\Auth;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Algorithms\Auth\AuthAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $algo = new AuthAlgo();
        return $algo->register(User::class, $request);
    }

    public function login(Request $request)
    {
        $algo = new AuthAlgo();
        return $algo->login(User::class, $request);
    }

    public function logout()
    {
        $algo = new AuthAlgo();
        return $algo->logout();
    }
}
