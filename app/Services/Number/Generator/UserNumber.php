<?php

namespace App\Services\Number\Generator;

use App\Models\User\User;
use App\Services\Number\BaseNumber;
use Illuminate\Database\Eloquent\Model;

class UserNumber extends BaseNumber
{
    /**
     * @var string
     */
    protected static string $prefix = "US";

    /**
     * @var Model|string|null
     */
    protected Model|string|null $model = User::class;


    public static function generate(): string
    {
        return parent::generate();
    }

}
