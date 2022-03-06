<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Route;
use Auth;

class UserControler extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except([
            'store',
            'login',
        ]);
    }

    public static function routes()
    {
        Route::resource('user', self::class);

        Route::get('me',            [self::class, 'me']) ->name('user.me');
        Route::post('user/login',   [self::class, 'login']) ->name('user.login');
    }

    /**
     * Display current user data.
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * Make login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|min:5',
            'password'  => 'required|min:5',
        ]);

        $loggged = Auth::attempt($request->only(['email', 'password']));

        if (!$loggged)
        {
            Auth::logout();

            return response()->json([
                'message' => 'Invalid credentials',
            ], 403);
        }

        $token = Auth::user()->createToken('login_via_api'.\Str::random(5));

        $plainTextToken = explode('|', $token->plainTextToken)[1] ?? null;

        return response()->json([
            'token' => $plainTextToken,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|min:5',
            'email'     => 'required|email|unique:users,email|min:5',
            'password'  => 'required|confirmed|min:5',
        ]);//TODO

        $user = User::create([
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'password'  => \Hash::make($request->input('password')),
        ]);

        if (!$user)
        {
            return response()->json([
                'message' => 'Fail to create user',
            ], 422);
        }

        $token = $user->createToken('login_via_api'.\Str::random(5));

        $plainTextToken = explode('|', $token->plainTextToken)[1] ?? null;

        return response()->json([
            'token' => $plainTextToken,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
