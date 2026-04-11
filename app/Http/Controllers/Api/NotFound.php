<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotFound extends Controller
{
    public function __invoke()
    {
        abort(404);
    }
}
