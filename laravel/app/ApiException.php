<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class ApiException extends \Exception
{

    /**
     * $apiErrConst 是我们在自定义错误码的时候 已经定义好的常量
     * 只需要传过来就行了
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function __construct(array $apiErrConst, Throwable $previous = null)
    {

        parent::__construct($apiErrConst[1], $apiErrConst[0], $previous);

    }
}