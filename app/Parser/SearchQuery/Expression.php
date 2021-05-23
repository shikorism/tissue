<?php

namespace App\Parser\SearchQuery;

class Expression
{
    /** @var bool */
    public $negative = false;

    /** @var string|null */
    public $target = null;

    /** @var string|null */
    public $keyword = null;
}
