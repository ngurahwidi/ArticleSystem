<?php

namespace App\Parser\User;

use App\Services\Constant\User\RoleUser;
use App\Services\Constant\User\StatusUser;
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

        $result = [
            'id' => $data->id,
            'username' => $data->username,
            'email' => $data->email,
            'phone' => $data->phone,
            'profile' => $data->profile,
            'status' => StatusUser::display($data->statusId),
            'role' => RoleUser::display($data->roleId),
            'bio' => $data->bio
        ];

        return $result;
    }

}
