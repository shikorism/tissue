<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentTagsController extends Controller
{
    public function __invoke(Request $request)
    {
        $ejaculations = Auth::user()
            ->ejaculations()
            ->with('tags')
            ->orderByDesc('ejaculated_date')
            ->limit(20);

        $tags = [];
        foreach ($ejaculations->get() as $ejaculation) {
            foreach ($ejaculation->tags as $tag) {
                $tags[$tag->name] = true;
            }
        }
        $tags = array_keys($tags);

        return response()->json($tags);
    }
}
