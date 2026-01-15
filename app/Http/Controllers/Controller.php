<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // [Add This]
use Illuminate\Foundation\Validation\ValidatesRequests;     // [Optional: Good to have]
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests; // [Add This]
}