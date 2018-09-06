<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private $rule = [
        'email' => 'required|email|max:50|unique:users',
        'password' => 'required|max:50',
        'name' => 'required|max:20|unique:users',
    ];

    private $message = [
        'name.required' => '姓名必须',
        'name.unique' => '该用户名已存在',
        'name.max' => '用户名最长20个字',
        'email.required'  => '邮箱必须',
        'email.email'  => '邮箱格式不正确',
        'email.max'  => '邮箱最大50个字',
        'email.unique'  => '该邮箱已存在',
        'password.required'  => '密码必须',
        'password.max'  => '密码最长50个字',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //auth:api 代表在 Authenticate 里 使用 guard => api  验证用户信息
        $this->middleware('auth:api',['except'=>['login','register','logout']]);
    }

    /**
     * @description register user
     *
     * @param
     * @return
     * @author guilong
     * @date 2018-08-02
     */
    public function register(){
        //直接输出错误
//        $this->validate($request, $this->rule,$this->message);
//        捕获错误
        $validator = Validator::make(request()->all(), $this->rule,$this->message);
        if($validator->fails()){
            $messages = $validator->errors();
            return response()->json([
                'code' => 1001,
                'msg' => $messages->first()
            ]);
        }

        $user = [
            'email' => request()->input('email'),
            'name' => request()->input('name'),
            'password' => bcrypt(request()->input('password')),
        ];
        try{
            //插入数据库
            $user_info = \App\User::create($user);
            //获取token
            $token = JWTAuth::fromUser($user_info);
            //更新token
            \App\User::where('id','=',$user_info['id'])->update(['api_token'=>$token]);
            //设置header头
        }
        catch(Exception $e){
//            var_dump($e->getMessage());
//            var_dump($e->getCode());
            return response()->json([
                'code' => 1002,
                'msg' => '注册失败'
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => '注册成功',
            'access_token' => $token
        ]);
    }

    public function login(){
        //直接输出错误
//        $this->validate($request, $this->rule,$this->message);
//        捕获错误
        $validator = Validator::make(request()->all(), ['email'=>'required|email|max:50','password'=>$this->rule['password']],$this->message);
        if($validator->fails()){
            $messages = $validator->errors();
            return response()->json([
                'code' => 1003,
                'msg' => $messages->first()
            ]);
        }

        $credentials = request(['email','password']);

        if(! $token = auth()->attempt($credentials)){
            return response()->json([
                'code' => 1004,
                'msg' => '登录失败'
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => '登录成功',
            'access_token' => $token,
        ]);
    }

    public function me(){

        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'code' => 1005,
                'msg' => '登录失败',
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => '登录成功',
            'data' => [
                'name' => $user['name'],
            ],
        ]);
    }

    public function logout(){
        try{
            auth()->logout();
        } catch (Exception $e) {
            return response()->json([
                'code' => 200,
                'msg' => 'logged out',
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => 'logged out',
        ]);
    }

    public function refresh(){
        return response()->json([
            'code' => 200,
            'msg' => '',
            'data' => [
                'access_token' => auth()->refresh(),
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL()*60
            ]
        ]);
    }


}
