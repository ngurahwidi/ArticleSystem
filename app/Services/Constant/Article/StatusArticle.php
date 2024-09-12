<?php

namespace App\Services\Constant\Article;

use App\Services\Constant\BaseIDName;

class StatusArticle extends BaseIDName
{
    const DRAFT_ID = 1;
    const DRAFT = 'Draft';

    const PUBLISH_ID = 2;
    const PUBLISH = 'Publish';

    const OPTION = [
        self::DRAFT_ID => self::DRAFT,
        self::PUBLISH_ID => self::PUBLISH,
    ];
}