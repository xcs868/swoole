<?php
/**
 * Created by PhpStorm.
 * User: Mr.lei
 * Date: 2020/04/17
 * Time: 18:59
 */

/**
 * 公共返回函数 用于构建返回信息
 *
 * @param [number] $code
 * @param [string] $info
 * @param array $options
 * @return void
 */
function Result($code, $info, $options = [])
{
    $default = [
        'code' => $code,
        'msg' => $info
    ];
    return array_merge($default, $options);
}

/**
 * 手机号中间四位星号处理
 */
function getMobile($mobile)
{
    return str_replace(substr($mobile, 3, 4), '****', $mobile);
}

/**
 * 姓名中间星号处理
 */
function getUserName($user_name)
{
    $strlen     = mb_strlen($user_name, 'utf-8');
    $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
    return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}