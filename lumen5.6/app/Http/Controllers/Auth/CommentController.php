<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Modules\Comment;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    protected $fields = [
        'body' => '',
    ];

    private $rule = [
        'aid' => 'required|min:1',
        'content_html' => 'required|max:5000',
    ];

    private $message = [
        'aid.required'  => '评论文章必须',
        'aid.min'  => '评论文章必须',
        'content_html.required'  => '评论必须',
        'content_html.max'  => '评论最大5000个字',
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
      * @description 列表
      *
      * @param
      * @return
      * @author guilong
      * @date 2018-08-10
    */
    public function fetchlists(){
        $page = request()->input('page',1);
        $limit = request()->input('limit',10);
        $aid = request()->input('aid',0); //文章id
        $offset = ($page-1)*$limit;
        $total = Comment::where(function ($where) use ($aid) {
                    if(!empty($aid)){
                        $where->where('post_id','=',$aid);
                    } else if(empty($aid)) {
                        $where->where('post_id','=',0);
                    }
                })->count();
        $list = Comment::offset($offset)
            ->where(function ($where) use ($aid) {
                if(!empty($aid)){
                    $where->where('post_id','=',$aid);
                } else if(empty($aid)) {
                    $where->where('post_id','=',0);
                }
            })
            ->select(
                'comments_article.created_at',
                'comments_article.id',
                'comments_article.body as content',
                'comments_article.parent_id as pid',
                'users.name as author')
            ->leftJoin('users','users.id','=','comments_article.user_id')
            ->limit($limit)
            ->orderBy('comments_article.created_at', 'desc')
            ->get();

        foreach($list as $k => $v){
            $v['created_at'] = date('Y-m-d H:i:s',strtotime($v['created_at']));
            $list[$k] = $v;
        }
        return response()->json([
            'code' => 200,
            'msg' => '',
            'total' => $total,
            'data' => $list
        ]);
    }

    /**
     * @description 打开详情
     *
     * @param
     * @return
     * @author guilong
     * @date 2018-08-10
     */
    public function info($id,$slug){
        try{
            $where = [];
            if($id){
                $where[] = ['posts.id', '=', $id];
            }else if($slug){
                $where[] = ['posts.slug', '=', $slug];
            }
            $field = [
                'posts.*'
                // 查询作者
                ,'users.name as author'
            ];
            if($id){ //后台页面
                $field[] = DB::raw('(select group_concat(t.id order by t.id asc) from '.DB::getTablePrefix().'tags t where t.id in (select v.tag_id from '.DB::getTablePrefix().'post_tag_pivot v where v.post_id = '.DB::getTablePrefix().'posts.id )) as tags');
            }
            else if($slug){  //用户页面
                $field[] = DB::raw('(select group_concat(t.tag order by t.id asc) from '.DB::getTablePrefix().'tags t where t.id in (select v.tag_id from '.DB::getTablePrefix().'post_tag_pivot v where v.post_id = '.DB::getTablePrefix().'posts.id )) as tags');
                $field[] = DB::raw('(select group_concat(t.title order by t.id asc) from '.DB::getTablePrefix().'tags t where t.id in (select v.tag_id from '.DB::getTablePrefix().'post_tag_pivot v where v.post_id = '.DB::getTablePrefix().'posts.id )) as tags_title');
            }
            // 查询标签
            $info = Post::where($where)
                ->select($field)
                ->leftJoin('users','users.id','=','posts.user_id')
                ->firstOrFail();
        } catch (Exception $e) {
            return response()->json([
                'code' => 1009,
                'msg' => '获取失败',
            ]);
        }
        $tags = !empty($info->tags) ? explode(',',$info->tags) : [];// 前端vuejs 使用了 element-ui 的 selected mupltiple 显示标签数组
        foreach($tags as $k => $v){
            if ($id) { //后台页面
                $v = intval($v);  // 前端vuejs 使用了 element-ui 的 selected mupltiple 显示标签， 标签的value必须是 int 类型
            }
            $tags[$k] = $v;
        }

        $data = [
            'id' => $info->id,
            'author' => $info->author,
            'tags' => $tags,
            'old_tags' => $info->tags,
        ];
        if ($slug) {  //用户页面
            unset($data['old_tags']);
            $data['tags_title'] = $info->tags_title ? explode(',',$info->tags_title) : [];

            //查询上一个同标签
            $tag = request()->input('tag','');
            $data['prev'] = Post::select('posts.slug','posts.title')
                ->where(function ($where) use ($tag,$info) {
                    if($tag){
                        // 1.根据 tag 查询 tag_id
                        // 2.根据 tag_id 查询 post_id
                        // 3.根据 post_id 查询 post列表
                        $where->whereIn('posts.id',DB::table('post_tag_pivot')->select('post_id')->where('tag_id','=',DB::raw("(select id from ".DB::getTablePrefix()."tags t where t.tag = '".$tag."')")));
                    }
                    $where->where('posts.published_at','>',$info->published_at);
                })
                ->orderBy('posts.published_at', 'asc')
                ->orderBy('posts.updated_at', 'asc')
                ->orderBy('posts.created_at', 'asc')
                ->first();

            //查询下一个同标签
            $tag = request()->input('tag','');
            $data['next'] = Post::select('posts.slug','posts.title')
                ->where(function ($where) use ($tag,$info) {
                    if($tag){
                        // 1.根据 tag 查询 tag_id
                        // 2.根据 tag_id 查询 post_id
                        // 3.根据 post_id 查询 post列表
                        $where->whereIn('posts.id',DB::table('post_tag_pivot')->select('post_id')->where('tag_id','=',DB::raw("(select id from ".DB::getTablePrefix()."tags t where t.tag = '".$tag."')")));
                    }
                    $where->where('posts.published_at','<',$info->published_at);
                })
                ->orderBy('posts.published_at', 'desc')
                ->orderBy('posts.updated_at', 'desc')
                ->orderBy('posts.created_at', 'desc')
                ->first();

            unset($data['id']);
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $info->$field);
        }

        return response()->json([
            'code' => 200,
            'msg' => '',
            'data' => $data,
        ]);
    }

    /**
     * @description 创建
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
                'code' => 1001,
                'msg' => $messages->first()
            ]);
        }

        $user = $this->getUserInfo();
        $time = date('Y-m-d H:i:s');
        $ip = request()->ip();

        //处理数据
        $post_data = [
            'user_id' => $user['id'],
            'post_id' => request()->input('aid',0),
            'parent_id' => request()->input('pid',0),
            'body' => request()->input('content_html',''),
            'created_at' => $time,
            'ip' => $ip,
        ];

        try{
            $id = Comment::insertGetId($post_data);
        }
        catch(Exception $e){
            DB::rollBack();
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
      * @description 修改
      *
      * @param
      * @return
      * @author guilong
      * @date 2018-08-10
    */
    public function update() {
        $id = request()->input('id',0);

        // 捕获错误
        $validator = Validator::make(request()->all(), $this->rule,$this->message);
        if($validator->fails()){
            $messages = $validator->errors();
            return response()->json([
                'code' => 1010,
                'msg' => $messages->first()
            ]);
        }

        $user = $this->getUserInfo();

        $uid = $user['id'];
        DB::beginTransaction();
        try{
            // 对比新标签和旧标签，获取差集 ，插入数据库
            $tags = request()->input('tags',[]);
            $oldtags = request()->input('old_tags','');
            $oldtags = explode(',',$oldtags);
            $diff = array_diff($tags,$oldtags);
            $diff_data = [];
            foreach($diff as $k => $v){
                $tmp = [
                    'post_id' => $id,
                    'tag_id' => $v,
                ];
                $diff_data[] = $tmp;
            }
            if(!empty($diff_data)){
                DB::table('post_tag_pivot')->insert($diff_data);
            }

            // 对比新标签和旧标签，获取交集 ，删除不存在的标签
            $intersect = array_intersect($tags,$oldtags);
            $intersect_data = [];
            foreach($oldtags as $k => $v){
                if(!in_array($v,$intersect)){ //不在交集里面，代表已删除了
                    $intersect_data[] = $v;
                }
            }
            if(!empty($intersect_data)){
                DB::table('post_tag_pivot')->where('post_id','=',$id)->whereIn('tag_id',$intersect_data)->delete();
            }

            // 更新数据
            $post_data = [];
            foreach (array_keys($this->fields) as $field) {
                $post_data[$field] = request()->get($field);
            }
            DB::table('posts')->where('id','=',$id)->where('user_id','=',$uid)->update($post_data);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
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
      * @description 删除
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
            DB::table('posts')->where('id', '=', $id)->where('user_id', '=', $uid)->delete();
            DB::table('post_tag_pivot')->where('post_id', '=', $id)->delete();

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

    /**
     * Recursive routine to set a unique slug
     *
     * @param string $title
     * @param mixed $extra
     */
    protected function setUniqueSlug($title, $extra)
    {
        $title = md5($title);
        $slug = str_slug($title.'-'.$extra);
        if (Post::whereSlug($slug)->exists()) {
            $this->setUniqueSlug($title, intval($extra) + 1);
            return;
        }

        return $slug;
    }

}
