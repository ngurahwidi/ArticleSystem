<?php

namespace App\Services\Constant\User;

use App\Services\Constant\BaseIDName;

class UserStatus extends BaseIDName
{
    const ACTIVE_ID = 1;
    const ACTIVE = 'active';

    const INACTIVE_ID = 2;
    const INACTIVE = 'inactive';

    const OPTION = [
        self::ACTIVE_ID => self::ACTIVE,
        self::INACTIVE_ID => self::INACTIVE
    ];

}
