<?php
/**
 * 定时器相关接口
 * 
 * Class Qiniu
 */
namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\ServerManager;

use EasySwoole\EasySwoole\Config as GlobalConfig;




class Timer extends Base
{  

    /**
     * 送礼物
     * 
     * @param string unionid  user unionid.
     * @param string playerId 活动id.
     * @param int    rid      红包id.
     * 
     * @return json | null.
     */
    public function pushProduct()
    {
        // 每隔 10 秒执行一次
        \EasySwoole\Component\Timer::getInstance()->loop(5 * 1000, function () {
            echo "this timer runs at intervals of 5 seconds\n";
        });
    }
}