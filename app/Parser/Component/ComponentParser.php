<?php

namespace App\Parser\Component;

use GlobalXtreme\Parser\BaseParser;
use App\Services\Constant\Global\ValidationStatus;

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

        return [
            'id' => $data->id,
            'name' => $data->name,
            'icon' => parse_link($data->icon),
            'status' => ValidationStatus::idName($data->statusId),
            'createdBy' => $data->createdBy,
            'createdByName' => $data->createdByName,
        ];
    }

    public static function brief($data)
    {
        if(!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'icon' => parse_link($data->icon),
            'status' => ValidationStatus::idName($data->statusId),
        ];
    }


}
