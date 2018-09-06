<?php
/*
 * Author:xx_lufei
 * Time:2016年11月3日13:15:51
 * Note:Access Control Headers.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsMiddleware
{
    private $headers;
    private $allow_origin;

    public function handle(Request $request, \Closure $next)
    {
        $this->headers = [
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Headers' => $request->header('Access-Control-Request-Headers'),
//            关键点：后端一般使用的域名是二级域名比如我的是api.xx.com,会和前端产生一个跨域的影响，请记得一定要设置
            'Access-Control-Expose-Headers' => 'Authorization',
//           设置跨域的时候还要设置一个Cache-Control,这个东西出现的问题真的是莫名其妙，坑了我很久
            'Cache-Control' => 'no-store', // 无的话会导致前端从缓存获取头token
            'Access-Control-Allow-Credentials' => 'true',//允许客户端发送cookie
            'Access-Control-Max-Age' => 1728000 //该字段可选，用来指定本次预检请求的有效期，在此期间，不用发出另一条预检请求。
        ];

        $this->allow_origin = [
            'http://localhost',
            'http://www.v.net',
            'http://localhost:8081',
        ];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        //如果origin不在允许列表内，直接返回403
        if (!in_array($origin, $this->allow_origin) && !empty($origin))
            return new Response('Forbidden', 403);
        //如果是复杂请求，先返回一个200，并allow该origin
        if ($request->isMethod('options'))
            return $this->setCorsHeaders(new Response('OK', 200), $origin);
        //如果是简单请求或者非跨域请求，则照常设置header
        $response = $next($request);
        $methodVariable = array($response, 'header');
        //这个判断是因为在开启session全局中间件之后，频繁的报header方法不存在，所以加上这个判断，存在header方法时才进行header的设置
        if (is_callable($methodVariable, false, $callable_name)) {
            return $this->setCorsHeaders($response, $origin);
        }
        return $response;
    }

    /**
     * @param $response
     * @return mixed
     */
    public function setCorsHeaders($response, $origin)
    {
        foreach ($this->headers as $key => $value) {
            $response->header($key, $value);
        }
        if (in_array($origin, $this->allow_origin)) {
            $response->header('Access-Control-Allow-Origin', $origin);
            //判断是否有access_token ，并设置在header头中，返回给前端
            if(isset($response->original['access_token']) && !empty($response->original['access_token'])){
                $response->header('Authorization', 'Bearer '.$response->original['access_token']);
            }
        } else {
            $response->header('Access-Control-Allow-Origin', '');
        }
        return $response;
    }
}