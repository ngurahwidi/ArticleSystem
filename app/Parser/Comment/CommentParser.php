<?php

namespace App\Parser\Comment;

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
    
            $result = [
                'id' => $data->id,
                'articleId' => $data->articleId,
                'userId' => $data->userId,
                'content' => $data->comment,
                'parentId' => $data->parentId, 
            ];
    
        return $result;
    }

}
