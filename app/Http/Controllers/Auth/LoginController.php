<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (auth()->check())
            auth()->logout();
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            if ($user->hasVerifiedEmail()) {
                $token = $user->createToken('Task Control Password Grant Client')->accessToken;
                $response = ['token' => $token, 'user' => $user];
                $user->update(['last_visited_at' => now()]);

                return response($response, 200);
            } else {
                //auth()->logout();
                return response(['message' => __('app.verify_email'), 'redirect' => 'email/verify'], 403);
            }
        } else {
            return response(['message' => __('auth.failed')], 422);
        }
    }

    public function logout(Request $request)
    {
        $result = [];
        try {
            $value = $request->bearerToken();
            $id = (new Parser())->parse($value)->getClaim('jti');
            $token = $request->user()->tokens->find($id);
            $token->revoke();
        } catch (\Exception $e) {
            $result = ['message' => $e->getMessage()];
        } finally {
            auth()->logout();
        }

        return response($result, 200);
    }
}
