<?php
/**
 * Created by w. 
 * User: w
 * Date: 2020-4-8
 * Time: 15:45
 */

namespace App\Task;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

use EasySwoole\Task\AbstractInterface\TaskInterface;

use EasySwoole\EasySwoole\Config as GlobalConfig;

use App\Storage\RedisSortedSets;

use App\Storage\OnlineUser;

/**
 * 广播弹幕
 * Class DanmuSendTask
 * @package App\Task
 */
class DanmuBroadCastTask implements TaskInterface
{
    protected $taskData;

    public function __construct($taskData)
    {
        $this->taskData = $taskData;
    }


    
     /**
     * 执行任务的内容
     * @param mixed $taskData     任务数据
     * @param int   $taskId       执行任务的task编号
     * @param int   $fromWorkerId 派发任务的worker进程号
     * @author : evalor <master@evalor.cn>
     */
    public function run(int $taskId, int $workerIndex)
    {
        $taskData  = $this->taskData;
        $server    = ServerManager::getInstance()->getSwooleServer();
       
        // $rdName    = GlobalConfig::getInstance()->getConf('REDIS_SORT_SET_NAME');
        // $redisName = GlobalConfig::getInstance()->getConf('TASK_REDIS_DANMU_SEND_NAME');
        
        // $redisSortedSets = new RedisSortedSets($redisName, $rdName);
        // $connList        = $redisSortedSets->getAll();

        // $connList = $server->connections;
        // foreach($connList as $fd) {
        //     $server->push($fd, $taskData);
        // }
        
        
        //var_dump(OnlineUser::getInstance()->table());
        // foreach(OnlineUser::getInstance()->table() as $row) {
        //     var_dump($row);
        // }
        foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
            $connection = $server->connection_info($userFd);
            
            if ($connection['websocket_status'] == 3) {  // 用户正常在线时可以进行消息推送
                $server->push($userInfo['fd'], $taskData);
            }
        }


        return true;
    }

     /**
     * 任务执行完的回调
     * @param mixed $result  任务执行完成返回的结果
     * @param int   $task_id 执行任务的task编号
     * @author : evalor <master@evalor.cn>
     */
    public function finish($result, $task_id)
    {
        // 任务执行完的处理
    }


    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        throw $throwable;
    }

}