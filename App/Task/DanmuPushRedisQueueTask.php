<?php
/**
 * 弹幕redis推送队列. 
 * User: w
 * Date: 2020-4-8
 * Time: 15:45
 */

namespace App\Task;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

use EasySwoole\Task\AbstractInterface\TaskInterface;

use EasySwoole\EasySwoole\Config as GlobalConfig;

use App\Storage\RedisQueue;

use EasySwoole\Queue\Job;
/**
 * 弹幕redis推送队列
 * Class SendDanmuTask
 * @package App\Task
 */
class DanmuPushRedisQueueTask implements TaskInterface
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
        $taskData = $this->taskData;
        $server          = ServerManager::getInstance()->getSwooleServer();
        $redisQueueName  = GlobalConfig::getInstance()->getConf('REDIS_QUEUE_NAME');
        $redisQueueName2 = GlobalConfig::getInstance()->getConf('REDIS_QUEUE_NAME_2');
        $redisName       = GlobalConfig::getInstance()->getConf('TASK_REDIS_DANMU_QUEUE_NAME');

        $redisQueue = new RedisQueue($redisName, $redisQueueName);
        

        $job = new Job();
        $job->setJobData($taskData);

        $redisQueue->push($job);


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