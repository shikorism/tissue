<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => 'required|string|regex:/^[a-zA-Z0-9_-]+$/u|max:15|unique:users|unique:deactivated_users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ];

        // reCAPTCHAのキーが設定されている場合、判定を有効化
        if (!empty(config('captcha.secret'))) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        return Validator::make(
            $data,
            $rules,
            ['name.regex' => 'ユーザー名には半角英数字とアンダーバー、ハイフンのみ使用できます。'],
            ['name' => 'ユーザー名']
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'display_name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_protected' => $data['is_protected'] ?? false,
            'accept_analytics' => $data['accept_analytics'] ?? false
        ]);
    }
}
