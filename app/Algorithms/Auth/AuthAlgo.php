<?php

namespace App\Algorithms\Auth;

use App\Models\User\User;
use App\Services\Number\BaseNumber;
use App\Services\Number\Generator\UserNumber;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Constant\Global\Path;
use App\Services\Constant\User\UserRole;
use App\Services\Constant\User\UserStatus;
use App\Services\Misc\FileUpload;

class AuthAlgo
{

    public function __construct(public ? User $user = null)
    {

    }
     /**
     * @param $model
     * @param Request $request
     *
     * @return JsonResponse|mixed
     */

    public function register(RegisterUserRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->user = $this->createUser($request);

                $this->uploadProfile($request);
            });

            return success($this->user);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function login(Request $request)
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
                'role' => UserRole::idName($user->roleId),
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

    private function createUser($request)
    {

        if(!in_array($request->roleId, UserRole::ROLE_OPTION)){
            errUserRole();
        }

        $user = User::create([
            'username' => $request->username,
            'number' => UserNumber::generate(),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'statusId' => UserStatus::ACTIVE_ID,
            'roleId' => $request->roleId,
            'bio' => $request->bio
        ]);
        if(!$user){
            errUserCreate();
        }

        return $user;
    }

    private function uploadProfile(Request $request)
    {
        if($request->hasFile('profile') && $request->file('profile')->isValid()) {

            $profile = $request->file('profile');
            $filePath = FileUpload::upload($profile, $request->username, Path::PROFILE);
        }

        $this->user->profile = $filePath;
        $this->user->save();
        return $this->user?->profile ?: null;
    }
}
