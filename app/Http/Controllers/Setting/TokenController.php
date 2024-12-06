<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Passport\TokenRepository;

class TokenController extends Controller
{
    /** @var TokenRepository */
    private $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function index()
    {
        $tokens = Auth::user()->tokens()
            ->select('oauth_access_tokens.*')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.revoked', false)
            ->where('oauth_clients.personal_access_client', true)
            ->get();
        $tokensLimit = User::PERSONAL_TOKEN_PER_USER_LIMIT;

        return view('setting.tokens')->with(compact('tokens', 'tokensLimit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('oauth_access_tokens', 'name')->where(function ($query) {
                    return $query->where('user_id', Auth::id())->where('revoked', false);
                })
            ]
        ], [], [
            'name' => '名前'
        ]);

        $existsCount = Auth::user()->tokens()
            ->select('oauth_access_tokens.*')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.revoked', false)
            ->where('oauth_clients.personal_access_client', true)
            ->count();

        if ($existsCount >= User::PERSONAL_TOKEN_PER_USER_LIMIT) {
            return redirect()->route('setting.tokens')
                ->with('status', User::PERSONAL_TOKEN_PER_USER_LIMIT . '件以上のトークンを作成することはできません。');
        }

        $token = Auth::user()->createToken($validated['name']);

        return redirect()->route('setting.tokens')->with([
            'status' => '作成しました。トークンを忘れずに控えてください。この画面を離れたら二度と確認できません！',
            'tokenId' => $token->token->id,
            'accessToken' => $token->accessToken
        ]);
    }

    public function revoke($id)
    {
        $token = $this->tokenRepository->findForUser($id, Auth::id());
        if ($token !== null) {
            $token->revoke();
        }

        return redirect()->route('setting.tokens')->with('status', '削除しました。');
    }
}
