<?php

namespace App\Algorithms\Auth;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Constant\User\RoleUser;
use App\Services\Constant\User\StatusUser;
use App\Services\Misc\FileUpload;

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
                if(!in_array($request->roleId, RoleUser::ROLE_OPTION)){
                    errNotFound("Role Not Found");
                }
                
                $user = $this->createUser($model, $request);

                $dataUser = $this->uploadProfile($user, $request);
    
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
                'role' => RoleUser::display($user->roleId),
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

    private function createUser($model, $request)
    {
        $user = $model::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'statusId' => StatusUser::ACTIVE_ID,
            'roleId' => $request->roleId,
            'bio' => $request->bio
        ]);

        return $user;
    }

    private function uploadProfile($model, $request)
    {
        $model->profile = $this->saveProfile($request);
        $model->save();
        return $model;
    }

    private function saveProfile(Request $request)
    {
        $profile = $request->file('profile');
        $name = $request->input('username');
        return FileUpload::upload($profile, $name, 'uploads/profile');
    }
}