<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function profile(){
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'code' => 401,
                'msg' => '登录失败',
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => '',
            'data' => [
                'name' => $user['name'],
            ],
        ]);
    }
}
