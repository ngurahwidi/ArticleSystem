<?php

namespace App\Algorithms\Auth;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\RegisterUserRequest;

class AuthAlgo
{
     /**
     * @param $model
     * @param Request $request
     *
     * @return JsonResponse|mixed
     */

    public function register($model, RegisterUserRequest $request)
    {
        try {
            $dataUser = DB::transaction(function () use ($model, $request) {
                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);
    
                $dataUser = 
                [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ];
                return $dataUser;
            });

            return success($dataUser);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function login($model, Request $request)
    {
        try {
            
            $token = Auth::guard('api')->attempt($request->only('email', 'password'));
            if (!$token) {
                errCredentialIncorrect("Please check your email or password!!");
            }

            $user = Auth::guard('api')->user();
            if (!$user) {
                errUnauthenticated("Can\'t get the user data!!");
            }

            return success([
                'id' => $user->id,
                'username' => $user->username,
                'token' => $token,
            ]);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function logout()
    {
        try {  
 
            JWTAuth::invalidate(JWTAuth::getToken());

            return success();
            
        } catch (\Exception $exception) {
            exception($exception);
        }
    }
}