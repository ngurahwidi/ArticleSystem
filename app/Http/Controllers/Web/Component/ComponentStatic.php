<?php

namespace App\Http\Controllers\Web\Component;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Constant\Global\ValidationStatus;

class ComponentStatic extends Controller
{
    public function validationStatus()
    {
        return success(ValidationStatus::get());
    }
}
