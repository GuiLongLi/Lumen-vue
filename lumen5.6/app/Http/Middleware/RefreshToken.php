<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RefreshToken extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //检查此次请求汇总是否带有token ,如果没有则抛出异常
        $this->checkForToken($request);

        try{
            //检查用户的登录状态，如果正常则通过
            if($this->auth->parseToken()->authenticate()){
                return $next($request);
            }

            throw new UnauthorizedHttpException('jwt-auth','未登录');
        }
        catch(TokenExpiredException $e){
            //此处捕获到了token 过期所抛出的 tokenexpiredexception 异常，我们在这里需要做的是刷新该用户的token
            try{
                //刷新用户的token
                $token = $this->auth->refresh();

                //使用一次性登录以保证此处请求的成功
                Auth::guard('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
            }
            catch(JWTException $e){
                //如果捕获到了异常，即代表refresh 也过期了 ,用户无法刷新令牌 ，需要重新登录
                throw new UnauthorizedHttpException('jwt-auth',$e->getMessage());
            }
        }
        //在响应头中返回新的token
        return $this->setAuthenticationHeader($next($request),$token);
    }
}
