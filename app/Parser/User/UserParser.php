<?php

namespace App\Parser\User;

use App\Services\Constant\User\UserRole;
use App\Services\Constant\User\UserStatus;
use GlobalXtreme\Parser\BaseParser;

class UserParser extends BaseParser
{
    /**
     * @param $data
     *
     * @return array|null
     */
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'username' => $data->username,
            'email' => $data->email,
            'phone' => $data->phone,
            'profile' => parse_link($data->profile),
            'status' => UserStatus::idName($data->statusId),
            'role' => UserRole::idName($data->roleId),
            'bio' => $data->bio
        ];
    }

    public static function simple($data)
    {
        return [
            'id' => $data->id,
            'username' => $data->username,
            'profile' => parse_link($data->profile),
        ];
    }

}
