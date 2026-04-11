<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Http\Controllers\Controller;
use App\Utilities\Formatter;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    public function __construct(protected Formatter $formatter)
    {
    }

    protected function normalizeQuery(string $query): string
    {
        return $this->formatter->normalizeTagName($query);
    }
}
