<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Modules\Tag;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    protected $fields = [
        'tag' => '',
        'title' => '',
        'subtitle' => '',
        'meta_description' => '',
        'page_image' => '',
        'reverse_direction' => 0,
    ];

    private $rule = [
        'tag' => 'required|max:100|unique:tags',
        'title' => 'required|max:100',
        'subtitle' => 'required|max:100',
    ];

    private $message = [
        'tag.required' => '标签必须',
        'tag.unique' => '该标签已存在',
        'tag.max' => '标签最长100个字',
        'title.required'  => '标题必须',
        'title.max'  => '标题最大100个字',
        'subtitle.required'  => '副标题必须',
        'subtitle.max'  => '副标题最大100个字',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //auth:api 代表在 Authenticate 里 使用 guard => api  验证用户信息
        $this->middleware('auth:api',['except'=>['fetchlists','info']]);
    }

    /**
      * @description 标签列表
      *
      * @param
      * @return
      * @author guilong
      * @date 2018-08-10
    */
    public function fetchlists(){
        // 传了id代表获取详情
        $id = request()->input('id',0);
        if(!empty($id)){
            return $this->info($id);
        }

        $page = request()->input('page',1);
        $limit = request()->input('limit',10);
        $offset = ($page-1)*$limit;
        $total = Tag::count();
        $list = Tag::offset($offset)
            ->limit($limit)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'code' => 200,
            'msg' => '',
            'total' => $total,
            'data' => $list
        ]);
    }

    /**
     * @description 打开标签详情
     *
     * @param
     * @return
     * @author guilong
     * @date 2018-08-10
     */
    public function info($id){
        try{
            $info = Tag::where('id', '=', $id)->firstOrFail();
        } catch (Exception $e) {
            return response()->json([
                'code' => 1009,
                'msg' => '获取失败',
            ]);
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $info->$field);
        }
        $data['reverse_direction'] = strval($data['reverse_direction']);  //element-ui 的 radio 标签的值，必须是字符串才能识别

        return response()->json([
            'code' => 200,
            'msg' => '',
            'data' => $data,
        ]);
    }

    /**
     * @description 创建标签
     *
     * @param
     * @return
     * @author guilong
     * @date 2018-08-10
     */
    public function store(){
        // 捕获错误
        $validator = Validator::make(request()->all(), $this->rule,$this->message);
        if($validator->fails()){
            $messages = $validator->errors();
            return response()->json([
                'code' => 1006,
                'msg' => $messages->first()
            ]);
        }

        $user = $this->getUserInfo();

        $tag = new Tag();
        foreach (array_keys($this->fields) as $field) {
            $tag->$field = request()->get($field);
        }
        try{
            $tag->user_id = $user['id'];
            $tag->save();
        }
        catch(Exception $e){
//            var_dump($e->getMessage());
//            var_dump($e->getCode());
            return response()->json([
                'code' => 1007,
                'msg' => '创建失败'
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => '创建成功',
        ]);
    }

    /**
      * @description 修改标签
      *
      * @param
      * @return
      * @author guilong
      * @date 2018-08-10
    */
    public function update() {
        $id = request()->input('id',0);
        // 捕获错误
        $validator = Validator::make(request()->all(), [
            // unique的用法： 'unique:users,email_address,'.$user->id.',user_id'  ==>  unique:表名, 字段名, 忽略的id, 忽略的id字段名称
            'tag'=> $this->rule['tag'].',tag,'.$id.',id',
            'title'=>$this->rule['title'],
            'subtitle'=>$this->rule['subtitle']
        ],$this->message);
        if($validator->fails()){
            $messages = $validator->errors();
            return response()->json([
                'code' => 1010,
                'msg' => $messages->first()
            ]);
        }

        $user = $this->getUserInfo();

        $uid = $user['id'];
        try{
            $tag = Tag::where('id', '=', $id)->where('user_id', '=', $uid)->firstOrFail();
            foreach (array_keys(array_except($this->fields, ['tag'])) as $field) {
                $tag->$field = request()->get($field);
            }
            $tag->save();
        } catch (Exception $e) {
            return response()->json([
                'code' => 1011,
                'msg' => '修改失败',
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => '修改成功',
        ]);
    }
    
    /**
      * @description 删除标签
      *
      * @param 
      * @return 
      * @author guilong
      * @date 2018-08-10
    */
    public function destroy(){
        $user = $this->getUserInfo();
        $id = request()->input('id');

        $uid = $user['id'];
        DB::beginTransaction();
        try{
            DB::table('tags')->where('id', '=', $id)->where('user_id', '=', $uid)->delete();
            DB::table('tags')->where('id', '=', $id)->where('user_id', '=', $uid)->delete();
            DB::table('post_tag_pivot')->where('tag_id', '=', $id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 1012,
                'msg' => '删除失败',
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    /**
      * @description 获取用户信息
      *
      * @param
      * @return
      * @author guilong
      * @date 2018-08-10
    */
    private function getUserInfo () {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'code' => 401,
                'msg' => '登录已失效,请重新登录',
            ]);
        }

        return $user;
    }
}
