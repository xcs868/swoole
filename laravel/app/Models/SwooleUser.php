<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use OSS\Result\Result;

class SwooleUser extends Model
{
    const MAP_UID_FD_PREFIX = 'chat_map_uid_fd:';
    const MAP_FD_UID_PREFIX = 'chat_map_fd_uid:';

    // fd与uid对应
    public function fdBindUid($fd, $uid)
    {
        return  Redis::setex(
            self::MAP_FD_UID_PREFIX . $fd,
            24 * 3600,
            $uid
        );
    }


    // 获取fd对应的uid
    public function getUidByFd($fd)
    {
        return Redis::get(self::MAP_FD_UID_PREFIX . $fd);
    }

    public function uidBindFd($uid, $fd)
    {
        return Redis::sadd(
            self::MAP_UID_FD_PREFIX . $uid,
            $fd
        );
    }

    // 获取uid全部fd，确保多端都能收到信息
    public function getFdsByUid($uid)
    {
        return Redis::sMembers(self::MAP_UID_FD_PREFIX . $uid);
    }

    // 删除uid的某个fd
    public function delFd($fd, $uid = null)
    {
        if (is_null($uid)) {
            $uid = $this->getUidByFd($fd);
        }

        if (!$uid) {
            return false;
        }

        Redis::srem(self::MAP_UID_FD_PREFIX . $uid, $fd);
        Redis::del(self::MAP_FD_UID_PREFIX . $fd);

        return true;
    }

    public function getUserName($uid)
    {
        $name = Db::table('users')->where('id',$uid)->value('name');

        return $name;
    }


    //群聊推送
    public function sendRoom()
    {
        $data =  Redis::keys(self::MAP_UID_FD_PREFIX . '*');

        return $data;
        return Result(1,'',[
            'data' => $data
        ]);
        foreach ($data as $k => $v){
            $result = Redis::sMembers($v);
            if(!empty($result)){
                foreach ($result as $key => $value){

                }
            }else{
                continue;
            }
        }
    }
}
