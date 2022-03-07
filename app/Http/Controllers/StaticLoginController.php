<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Route;
use Auth;

class StaticLoginController extends Controller
{
    public static function routes()
    {
        Route::get('login-as/{userId}', [self::class, 'loginAs'])->name('static_login_as');
    }

    /**
     * function index
     *
     * @param Request $request
     * @return
     */
    public function index(Request $request)
    {
        return view('welcome', [
            'users' => User::paginate(10),
        ]);
    }

    /**
     * function loginAs
     *
     * @param Request $request, int $loginAsUserId
     * @return
     */
    public function loginAs(Request $request, int $loginAsUserId)
    {
        $user = User::find($loginAsUserId);

        if (!$user)
        {
            return redirect()->route('site_root')->with('error', 'User not found!');
        }

        $token = $user->createToken('login_via_api'.\Str::random(5));

        $plainTextToken = explode('|', $token->plainTextToken)[1] ?? null;

        Auth::login($user);

        session()->put('token', $plainTextToken);

        return redirect()->route('messages_index')->with('error', 'Loged in');
    }
}
