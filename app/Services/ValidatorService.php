<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class ValidatorService
{
    public static function make($request, $field)
    {
        return Validator::make($request->all(), $field, MESSAGE_VALIDATE, FIELD_VALIDATE);
    }
}
