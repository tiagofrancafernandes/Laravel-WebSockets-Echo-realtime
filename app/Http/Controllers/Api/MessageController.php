<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Route;
use Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public static function routes()
    {
        Route::prefix('messages')->group(function () {
            Route::get('/',                 [self::class, 'index'])->name('messages.index');
            Route::post('/',                [self::class, 'sendTo'])->name('messages.send_message');
            Route::get('/from/{userId}',   [self::class, 'listFrom'])->name('messages.from');
        });
    }

    /**
     * function index
     *
     * @param Request $request
     * @return
     */
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|integer',
        ]);

        $query = Message::where('to', Auth::user()->id);

        if($request->input('from')) {
            $query = $query->where('from', $request->input('from'));
        }

        return response()->json([
            'data' => $query->paginate(10),
        ], 200);
    }

    /**
     * function listFrom
     *
     * @param int $userId
     * @param Request $request
     * @return
     */
    public function listFrom(Request $request, int $userId)
    {
        $query = Message::where('to', Auth::user()->id);

        return response()->json([
            'data' => $query->where('from', $userId)->paginate(10),
        ], 200);
    }

    /**
     * function sendTo
     *
     * @param Request $request
     * @return
     */
    public function sendTo(Request $request)
    {
        $request->validate([
            'to'      => 'required|integer|exists:users,id',
            'message' => 'required|string|min:3',
        ]);

        $userId = Auth::user()->id;

        if ($request->input('to') == $userId)
        {
            return response()->json([
                'message' => 'You can\'t send message to yourself',
            ], 422);
        }

        $message = Message::create([
            'to'      => $request->input('to'),
            'message' => $request->input('message'),
            'from'    => $userId,
            'readed'  => false,
        ]);

        if (!$message)
        {
            return response()->json([
                'message' => 'Fail to send message',
            ], 422);
        }

        return response()->json([
            'message' => 'Message sended successfully',
        ], 201);
    }
}
