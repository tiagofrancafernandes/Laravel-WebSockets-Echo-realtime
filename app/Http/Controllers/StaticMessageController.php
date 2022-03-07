<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Route;
use Auth;

class StaticMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public static function routes()
    {
        Route::get('messages', [self::class, 'index'])->name('messages_index');
    }

    /**
     * function index
     *
     * @param Request $request
     * @return
     */
    public function index(Request $request)
    {
        $token = session()->get('token', null);

        if (!$token)
        {
            $token = Auth::user()->createToken('login_via_api'.\Str::random(5));

            $plainTextToken = explode('|', $token->plainTextToken)[1] ?? null;
            $token = $plainTextToken;
        }

        return view('messages.index', [
            'token' => $token,
        ]);
    }
}
