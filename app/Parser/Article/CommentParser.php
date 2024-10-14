<?php

namespace App\Parser\Article;

use App\Parser\User\UserParser;
use GlobalXtreme\Parser\BaseParser;

class CommentParser extends BaseParser
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
                'user' => UserParser::simple($data->user),
                'comment' => $data->comment,
                'replies' => self::replies($data->replies),
            ];
    }

    /** --- FUNCTIONS --- */

    private static function replies($subs)
    {
        if (!$subs || count($subs) == 0) {
            return null;
        }

        $result = [];
        foreach ($subs as $sub) {
            $result[] = [
                'id' => $sub->id,
                'user' => UserParser::simple($sub->user),
                'content' => $sub->comment,
                'replies' => self::replies($sub->replies),
            ];
        }

        return $result;
    }

}
