<?php

namespace App\Services\Constant\Global;

class Path
{
    const ARTICLE = 'articles/';
    const ARTICLE_GALLERY = self::ARTICLE . 'galleries/';
    const PROFILE = 'profiles/';
    const COMPONENT_CATEGORY = 'components/category/';
    const COMPONENT_TAG = 'components/tag/';

    /** ---STATIC FUNCTION --- */

    public static function STORAGE_PUBLIC_PATH($filepath)
    {
        return storage_path('app/public/' . $filepath);
    }
}
