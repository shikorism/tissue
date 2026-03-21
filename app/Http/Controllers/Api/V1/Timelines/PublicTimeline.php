<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Timelines;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\Http\Resources\EjaculationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PublicTimeline extends Controller
{
    public function __invoke(Request $request)
    {
        abort(410, 'this endpoint is no longer available.');
    }
}
