<?php


namespace App\Contracts\Interfaces;


interface Sms
{
    /**
     * @param string $phone 手机号
     * @return bool 是否发送成功
     */
    public function send(string $phone): bool;

    /**
     * @param string $phone 手机号
     * @param string $code 用户填写的验证码
     * @return bool 是否验证通过
     */
    public function check(string $phone, string $code): bool;
}