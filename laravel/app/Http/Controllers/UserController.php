<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User as Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\SwooleUser;

class UserController extends Controller
{

    public function index()
    {
        //$user = Db::table('users')->get();

        return Result(200,'',[
            'data' => 'User下的Index方法'
        ]);
    }


    public function getUser()
    {
        $user = Member::find(3);
        return Result(200,'User/getUser',[
            'data' => $user
        ]);
        //return response()->json($user);
    }


    public function login()
    {

        $credentials = request(['mobile', 'code','token']);
        $Phone = $credentials['mobile'];
        $Code = $credentials['code'];

        //验证手机号
        if(strlen($Phone) != 11) return Result(0,'请输入正确的手机号码!');

//        $PhoneRedisCode = Redis::get('SendSmsCode_'.$Phone);
//        if(!$PhoneRedisCode){
//            return response()->json(['code' => 401, 'message' => '请先发送短信验证码!']);
//        }
//
//        if($Code !== $PhoneRedisCode){
//            return response()->json(['code' => 401, 'message' => '验证码错误或已过期!']);
//        }

        //根据手机号码获取当前用户id
        $userId = 1;

        //获取redis中用户的jwt-token
        $CacheToken = Redis::get('UserToken_'.$userId);

        //如果当前用户redis中存在jwt-token的情况下
        if(!empty($CacheToken)){
            try {
                //验证redis中的jwt-token是否有效
                $check = auth('api')->setToken($CacheToken)->check();
                if($check){
                    //如果有效的话将其作废掉，主要应用于用户重复登录的场景，顶下线机制
                    auth('api')->setToken($CacheToken)->invalidate();
                }
            } catch (\Exception $exception) {
                Log::error('The func UserEntry invalidate token error is: '.$exception->getMessage());
                return response()->json(['code' => 500, 'message' => '登录失败，请稍后再试!']);
            }
        }

        //生成jwt-token，我这里采用用户id和手机号作为加密的参数。
        //ps：attempt方法会自动验证user表中两个字段作为where条件的记录，如果查不到数据或字段错误，这里会返回false。
        //我会在注意事项中说明一下关于自动验证表字段的问题
        if(!$token = auth('api')->attempt(['id' => $userId, 'mobile' => $Phone])){
            return response()->json(['code' => 401, 'message' => '用户信息异常,请稍后尝试!']);
        }

        //将用户最新的jwt_token存入redis
        Redis::set('UserToken_'.$userId,$token);
        return response()->json(['code' => 200 ,'token' => 'bearer '.$token]);
    }




    /**
     * Swoole聊天室相关逻辑
    */

    /**
     * 登陆
     */
    public function user_login()
    {
        //
    }


    /**
     * 注册
    */
    public function register()
    {
//        $userFdRelation = new SwooleUser();
//        $fds = $userFdRelation->getFdsByUid(1);
//        foreach ((array)$fds as $fd) {
//           var_dump($fd);
//        }
//        var_dump($fds);die;
//        $A =  Redis::sMembers('chat_map_uid_fd:*');
//        $data =  Redis::keys('chat_map_uid_fd:*');
//
//        foreach ($data as $k => $v){
//           $restl =  Redis::sMembers($v);
//           var_dump($restl);die;
//        }

        $credentials = request(['name', 'password','mobile']);
        if (empty($credentials)) return Result(0,'无参数');

        $name = $credentials['name'];
        $mobile = $credentials['mobile'];
        $password = $credentials['password'];

        if (empty($name)) return Result(0,'请输入用户名');
        if (empty($password)) return Result(0,'密码不允许为空！');
        if (strlen($mobile) != 11) return Result(0,'请输入正确的手机号码!');

        $check = "/^(((13[0-9])|(14[579])|(15([0-3]|[5-9]))|(16[6])|(17[0135678])|(18[0-9])|(19[89]))\\d{8})$/";

        if(!preg_match($check, $mobile)){
            //这里有无限想象
            return Result(0,'请输入正确的手机号码！');
        }


        try {
            //验证手机号码是否已被注册
            $user = Db::table('users')->where('mobile',$mobile)->first();

            if (!empty($user)) {
                return Result(200,'该手机号码已被注册,登陆成功',[
                    'uid' => $user->id,
                    'name' => $user->name,
                    'pass' => $user->password,
                    'tel' => $mobile
                ]);
            }


            $id = Db::table('users')->insertGetId([
                'name' => $name,
                'password' => $password,
                'mobile' => $mobile,
                'created_time' => date('Y-m-d H:i:s'),
                'register_time' => time(),
            ]);

            return Result(200,'注册成功',[
                'uid' => $id,
                'name' => $name,
                'tel' => $mobile,
                'pass' => $password
            ]);
        }catch (\PDOException $e){
            return Result(500, $e->getMessage());
        }


    }

}
