<?php

namespace App\Services\Constant\Global;

use App\Services\Constant\BaseIDName;

class ValidationStatus extends BaseIDName
{
    const DRAFT_ID = 1;
    const DRAFT = 'Draft';
    const PUBLISH_ID = 2;
    const PUBLISH = 'Publish';
    const ARCHIVED_ID = 3;
    const ARCHIVED = 'Archived';

    const OPTION = [
        self::DRAFT_ID => self::DRAFT,
        self::PUBLISH_ID => self::PUBLISH,
        self::ARCHIVED_ID => self::ARCHIVED
    ];

    const VALIDATION_STATUS = [
        self::DRAFT_ID,
        self::PUBLISH_ID,
        self::ARCHIVED_ID
    ];
}
