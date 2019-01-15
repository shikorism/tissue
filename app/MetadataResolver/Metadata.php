<?php

namespace App\MetadataResolver;

use Carbon\Carbon;

class Metadata
{
    public $title = '';
    public $description = '';
    public $image = '';
    /** @var Carbon|null */
    public $expires_at = null;
}
