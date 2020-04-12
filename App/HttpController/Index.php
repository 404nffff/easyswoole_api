<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

use App\Models\DanmuList;

use EasySwoole\EasySwoole\Task\TaskManager;


use App\Task\DanmuPushRedisQueueTask;
use App\Task\DanmuBroadCastTask;

use App\Task\MysqlPullDanmuRedisQueueTask;

use EasySwoole\EasySwoole\Config as GlobalConfig;

use App\Storage\OnlineUser;


use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\Response;

class Index extends Controller
{

     /**
     * 默认指向控制器
     * @param RouteCollector $routeCollector.
     * 
     * @return void.
     */
    function index()
    {
        // $ret = [];
        // $client = Rpc::getInstance()->client();
        // /*
        //  * 调用商品列表
        //  */
        // $client->addCall('goods','list',['page'=>1])
        //     ->setOnSuccess(function (Response $response)use(&$ret){
        //         $ret['goods'] = $response->toArray();
        //     })->setOnFail(function (Response $response)use(&$ret){
        //         $ret['goods'] = $response->toArray();
        //     });

        // $client->exec(2.0);

        // $this->writeJson(200,$ret);

        go(function (){
            $ret = [];
        
            $wait = new \EasySwoole\Component\WaitGroup();
        
            $wait->add();
            go(function ()use($wait,&$ret){
                \co::sleep(0.1);
                $ret[] = time();
                $wait->done();
            });
        
            $wait->add();
            go(function ()use($wait,&$ret){
                \co::sleep(2);
                $ret[] = time();
                $wait->done();
            });
        
            $wait->wait();
        
            var_dump($ret);
        });
    }

}