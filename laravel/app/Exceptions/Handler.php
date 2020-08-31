<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
//    public function render($request, Exception $exception)
//    {
//        return parent::render($request, $exception);
//    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
//    public function render($request, Exception $exception)
//    {
//
//        // api接口主动抛出的异常
//        if ($exception instanceof ApiException) {
//            $code = $exception->getCode();
//            $msg  = $exception->getMessage();
//        }
//        // 非API接口的异常在else中执行
//        else{
//            // 这里是laravel框架的异常处理方式
//            // return parent::render($request, $exception);
//
//            // 这里是我们自定义的异常处理方式
//            $code = $exception->getCode();
//            if (!$code || $code<0){
////                $code = ApiErrDesc::UNKNOWN_ERROR[0];
//            }
//            $msg = $exception->getMessage();
////            $msg = $exception->getMessage()?: ApiErrDesc::UNKNOWN_ERROR[1];
//        }
//
//        $content = [
//            'code' => $code,
//            'msg'  => $msg,
//            'data' => []
//        ];
//        // 异常返回
//        return response()->json($content);
//    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

//        if ($exception instanceof UnauthorizedHttpException) {
//            $preException = $exception->getPrevious();
//            if ($preException instanceof
//                \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
//                return response()->json(['error' => 'TOKEN_EXPIRED']);
//            } else if ($preException instanceof
//                \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
//                return response()->json(['error' => 'TOKEN_INVALID']);
//            } else if ($preException instanceof
//                \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
//                return response()->json(['error' => 'TOKEN_BLACKLISTED']);
//            }
//            if ($exception->getMessage() === 'Token not provided') {
//                return response()->json(['error' => 'Token not provided']);
//            }
//        }
//        // 参数验证错误的异常，我们需要返回 400 的 http code 和一句错误信息
        if ($exception instanceof ValidationException) {
            return response($exception->errors(),400);
//            return response(['error' => array_first(array_collapse($exception->errors()))], 400);
        }
        // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
        if ($exception instanceof UnauthorizedHttpException) {
            return response($exception->getMessage(), 401);
        }

        return parent::render($request, $exception);
    }
}
