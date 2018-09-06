<?php

// api/v1的接口
$api_v1 = 'api/v1';
Route::group([
    'middleware' => 'api',  //这个本来用来实现api接口的处理，暂未用到
    'prefix' => $api_v1.'/auth'
], function ($app) {
    $app->post('register', 'Auth\AuthController@register');  //注册
    $app->post('login', 'Auth\AuthController@login');  //登录
    $app->post('logout', 'Auth\AuthController@logout'); //登出
    $app->post('refresh', 'Auth\AuthController@refresh'); //刷新token
    $app->post('me', 'Auth\AuthController@me'); //获取个人信息
});

Route::group(['prefix'=>$api_v1],function($app){
    $app->get('tag','Auth\TagController@fetchlists'); //标签列表 & 标签详情
    $app->get('article','Auth\PostController@fetchlists'); //文章列表 & 文章详情
    $app->get('comment','Auth\CommentController@fetchlists'); //评论列表
});

Route::group(['middleware'=>'refresh.token','prefix'=>$api_v1],function($app){
    $app->get('profile','User\UserController@profile');  //个人中心

    $app->post('tag','Auth\TagController@store'); //标签创建
    $app->put('tag','Auth\TagController@update'); //标签修改
    $app->delete('tag','Auth\TagController@destroy'); //标签删除

    $app->post('article','Auth\PostController@store'); //文章创建
    $app->put('article','Auth\PostController@update'); //文章修改
    $app->delete('article','Auth\PostController@destroy'); //文章删除

    $app->post('comment','Auth\CommentController@store'); //发表评论

});
