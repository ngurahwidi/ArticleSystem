<?php

namespace App\Services\Constant\User;

use App\Services\Constant\BaseIDName;

class UserRole extends BaseIDName
{
    const ADMIN_ID = 1;
    const ADMIN = 'admin';
    const AUTHOR_ID = 2;
    const AUTHOR = 'author';
    const SUBSCRIBER_ID = 3;
    const SUBSCRIBER = 'subscriber';

    const OPTION = [
        self::ADMIN_ID => self::ADMIN,
        self::AUTHOR_ID => self::AUTHOR,
        self::SUBSCRIBER_ID => self::SUBSCRIBER
    ];

    const ROLE_OPTION = [
        self::ADMIN_ID,
        self::AUTHOR_ID,
        self::SUBSCRIBER_ID
    ];

}
