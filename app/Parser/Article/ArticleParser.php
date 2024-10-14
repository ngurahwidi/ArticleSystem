<?php

namespace App\Parser\Article;

use App\Parser\User\UserParser;
use App\Services\Constant\Global\ValidationStatus;
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

        return [
            'id' => $data->id,
            'title' => $data->title,
            'user' => UserParser::simple($data->user),
            'description' => $data->description,
            'content' => $data->content,
            'galleries' => $data->galleryLinks(),
            'featuredImage' => parse_link($data->featuredImage),
            'status' => ValidationStatus::idName($data->statusId),
            'categories' => $data->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            }),
            'tags' => $data->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            }),
        ];

    }

    public static function brief($data)
    {
        if(!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'description' => $data->description,
            'featuredImage' => parse_link($data->featuredImage),
            'status' => ValidationStatus::idName($data->statusId),
            'popular' => $data->popular,
        ];
    }

}
