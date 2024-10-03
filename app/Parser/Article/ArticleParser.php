<?php

namespace App\Parser\Article;

use Carbon\Carbon;
use GlobalXtreme\Parser\BaseParser;
use Illuminate\Support\Facades\Storage;
use App\Services\Constant\Global\StatusId;
use App\Services\Constant\Article\StatusArticle;
use App\Services\Constant\Global\StatusValidation;

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
            'galleries' => $data->galleries,
            'status' => StatusValidation::display($data->statusId),
            'categories' => $data->categories->pluck('name'),
            'tags' => $data->tags->pluck('name'),
            'author' => $data->createdByName,
            'comments' => $data->comments,
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
