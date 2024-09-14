<?php

namespace App\Parser\Article;

use App\Services\Constant\Article\StatusArticle;
use Carbon\Carbon;
use GlobalXtreme\Parser\BaseParser;

class ArticleParser extends BaseParser
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
            'title' => $data->title,
            'userId' => $data->userId,
            'description' => $data->description,
            'content' => $data->content,
            'gallery' => $data->gallery,
            'status' => StatusArticle::display($data->statusId),
            'author' => $data->createdByName,
            'createdAt' => Carbon::parse($data->createdAt)->format('d-m-Y H:i:s'),
        ];

        return $result;

    }

    public static function brief($data)
    {
        if(!$data) {
            return null;
        }

        $result = [
            'id' => $data->id,
            'title' => $data->title,
            'description' => $data->description,
            'image' => $data->filepath,
            'status' => $data->statusId
        ];

        return $result;
    }

}
