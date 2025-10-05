<?php

namespace App\Http\Controllers\Api;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:ejaculations'
        ]);

        $keys = [
            'user_id' => Auth::id(),
            'ejaculation_id' => $request->input('id')
        ];

        $like = Like::query()->where($keys)->first();
        if ($like) {
            $data = [
                'errors' => [
                    ['message' => 'このチェックインはすでにいいね済です。']
                ],
                'ejaculation' => [
                    'id' => $like->ejaculation->id,
                    'is_liked' => $like->ejaculation->is_liked,
                    'likes_count' => $like->ejaculation->likes_count,
                ]
            ];

            return response()->json($data, 409);
        }

        $like = Like::create($keys);

        return [
            'ejaculation' => [
                'id' => $like->ejaculation->id,
                'is_liked' => $like->ejaculation->is_liked,
                'likes_count' => $like->ejaculation->likes_count,
            ]
        ];
    }

    public function destroy($id)
    {
        Validator::make(compact('id'), [
            'id' => 'required|integer'
        ])->validate();

        $like = Like::query()->where([
            'user_id' => Auth::id(),
            'ejaculation_id' => $id
        ])->first();
        if ($like === null) {
            $ejaculation = Ejaculation::find($id);

            $data = [
                'errors' => [
                    ['message' => 'このチェックインはいいねされていません。']
                ],
                'ejaculation' => [
                    'id' => $ejaculation->id,
                    'is_liked' => $ejaculation->is_liked,
                    'likes_count' => $ejaculation->likes_count,
                ]
            ];

            return response()->json($data, 404);
        }

        $like->delete();

        return [
            'ejaculation' => [
                'id' => $like->ejaculation->id,
                'is_liked' => $like->ejaculation->is_liked,
                'likes_count' => $like->ejaculation->likes_count,
            ]
        ];
    }
}
