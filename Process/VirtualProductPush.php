<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/10/18 0018
 * Time: 10:28
 */

namespace App\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Config as GlobalConfig;

use Swoole\Process;

use EasySwoole\Redis\Redis;
use EasySwoole\RedisPool\RedisPool;

use \EasySwoole\Pool\Manager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;

use App\Task\DanmuPushRedisQueueTask;
use App\Task\DanmuBroadCastTask;

use App\Storage\OnlineUser;

use App\Storage\RedisSub;


use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;


use App\Models\PlayerGoods;

class VirtualProductPush extends AbstractProcess
{

   /*
    * 当进程启动后，会执行的回调
    */
    protected function run($arg)
    {
        $this->config = GlobalConfig::getInstance();

        $time = $this->config->getConf('PROCESS_TIMER_TIME');
        
        //{"id":"11","player_goods_id":27,"whatNum":1,"second":"1","add_people":"1","long":"1","virtual_status":true}
        
        $this->addTick($time, function (){
            
         
        });

    }


    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
    }

    /*
    * 公共推送
    */
    private function commonPush() 
    {
        go(function (){
            $csp = new \EasySwoole\Component\Csp();

            //推送弹幕到 Redis队列 协程异步
            $csp->add('t1',function (){

                $task = TaskManager::getInstance();
                $task->async(new DanmuBroadCastTask($this->roomId, $this->content, $this->username, $this->action));
                
                \co::sleep(0.1);
               return 't1 result';
            });
             $csp->exec();
        });
    }

}