<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Swoole\WebSocket\Server as WebSockServer;
use App\Models\SwooleUser;
use Illuminate\Support\Facades\Redis;

class Swoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:swoole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $server;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->start();
    }

    /**
     * 初始化Swoole连接
     *
     * @return mixed
     */
    private function start()
    {
        $this->server = new WebSockServer("0.0.0.0",8888);
        $this->server->set([
            'heartbeat_check_interval' => 60,
            'heartbeat_idle_time' => 600,
        ]);

        $this->server->on('open', function (WebSockServer $server, $request) {
            echo "成功连接，您的客户端id是-》{$request->fd}\n";
        });

        $this->server->on('message', function (WebSockServer $server, $frame) {
            echo "服务端接受到到数据： {$frame->fd}:{$frame->data}\n";


            $data = json_decode($frame->data, true);
            echo "{$data['type']}";

            return $this->SendTypeSwoole($data,$server ,$frame);
//            $result = $this->SendTypeSwoole($data,$server ,$frame);
//            $server->push($frame->fd, "客户端接收服务端返回数据");
        });


        $this->server->on('request', function ($request, $response) {
            echo "接收http请求从get获取：{$request->get['message']}";
            // 接收http请求从get获取message参数的值，给用户推送
            // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
            foreach ($this->server->connections as $fd) {
                // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                if ($this->server->isEstablished($fd)) {
                    $this->server->push($fd, $request->get['message']);
                }
            }
        });

        $this->server->on('close', function (WebSockServer $server, $fd) {
            echo "连接 {$fd} 关闭!\n";
            $userFdRelationSwoole = new SwooleUser();
            $userFdRelationSwoole->delFd($fd);
        });

        $this->server->start();
    }


    /**
     * 根据前端返回类型发送对应数据
     *
     * @param $type  String
     * @param $server Object
    */
    protected function SendTypeSwoole($data,$server,$frame)
    {
        //用户相关逻辑处理
        $userFdRelation = new SwooleUser();
        switch ($data['type']) {
            case 'init':
                $userFdRelation->uidBindFd($data['uid'], $frame->fd);
                $userFdRelation->fdBindUid($frame->fd, $data['uid']);
                $fds = $userFdRelation->getFdsByUid($data['uid']);
                foreach ((array)$fds as $fd) {
                    !$server->exist($fd) && $userFdRelation->delFd($fd, $data['uid']);
                }

                $server->push(
                    $frame->fd,
                    json_encode([
                        'code' => 200,
                        'msg' => 'swoole yes',
                    ])
                );
                break;
            case 'send':
                $data['from'] = $userFdRelation->getUidByFd($frame->fd);
                // 验证是否已经注册
                if (!$data['from']) {
                    $server->push(
                        $frame->fd,
                        json_encode([
                            'code' => -1,
                            'msg' => '发送失败，未登入',
                        ])
                    );
                    return;
                }

                // 将消息推送到uid各端
//                $fds = $userFdRelation->getFdsByUid($data['uid']);
                //此处仅仅为测试群聊
                $res = $userFdRelation->sendRoom();

                try {
                    //根据uid查询用户名称
                    $name = $userFdRelation->getUserName($data['uid']);

                    foreach ($res as $v) {
                        $result = Redis::sMembers($v);
                        if (empty($result)) {
                            continue;
                        } else {
                            foreach ($result as $key => $value) {
                                if ($server->exist((int)$value)) {
                                    $server->push(
                                        (int)$value,
                                        json_encode([
                                            'code' => 1,
                                            'ss' => $name,
                                            'con' => $data['content'],
                                            'from' => $data['from'],
                                            'uid' => $data['uid']
                                        ])
                                    );
                                }
                            }
                        }
                    }
//                    foreach ((array)$fds as $fd) {
//                        if($server->exist((int)$fd)){
//                            echo '2222222';
//                            $server->push(
//                                (int)$fd,
//                                json_encode([
//                                    'code' => 1,
//                                    'ss' => $name,
//                                    'con' => $data['content'],
//                                    'from' => $data['from'],
//                                    'uid' => $data['uid']
//                                ])
//                            );
//                        }else{
//                            echo "发送失败";
//                        }
//                    }
                }catch (\Exception $exception) {
                    echo '错误:'.$exception->getMessage();
                }
                // 告知推送成功
                // $server->push($frame->fd, json_encode(['status' => 1, 'message' => 'sent']));
                break;
            default:
                // 非法请求
                $server->push(
                    $frame->fd,
                    json_encode([
                        'code' => 401,
                        'msg' => '非法请求',
                        'disconnect' => 1, // 告知终端请断开
                    ])
                );
        }
    }
}
