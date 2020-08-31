<?php


namespace App\Tools;

use App\Contracts\Interfaces\Sms;

class MeiSms implements Sms
{
    public function send($phone): bool
    {
        $code = mt_rand(1e3, 1e4 - 1);
        // TODO ... 通过接口发送
        // 存放验证码到 Redis
        $cacheManager = cache();
        // 设定 5 分钟失效
        $cacheManager->set('sms:' . $phone, $code, 5 * 60);
        return true;
    }

    public function check($phone, $code): bool
    {
        $cacheManager = cache();
        return $cacheManager->get('sms:' . $phone) === $code;
    }
}