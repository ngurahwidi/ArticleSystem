<?php

namespace App\Parser\Component;

use GlobalXtreme\Parser\BaseParser;
use App\Services\Constant\Global\StatusValidation;

class ComponentParser extends BaseParser
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
            'name' => $data->name,
            'icon' => $data->icon,
            'status' => StatusValidation::display($data->statusId),
            'authorId' => $data->userId,
            'author' => $data->createdByName,
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
            'name' => $data->name,
            'icon' => $data->icon,
            'status' => StatusValidation::display($data->statusId),
        ];

        return $result;
    }

}
