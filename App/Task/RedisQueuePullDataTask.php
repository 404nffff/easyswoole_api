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
class RedisQueuePullDataTask implements TaskInterface
{
    protected $redisName;
    protected $redisQueue;


     /**
     * 初始化
     * @param array  $taskData   推送数据
     * @param string $redisName  redis注册名称
     * @param string $redisQueue redis队列名称
     */
    public function __construct($redisName, $redisQueue)
    {
        $this->redisName  = $redisName;
        $this->redisQueue = $redisQueue;
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
        $redisQueue = new RedisQueue($this->redisName, $this->redisQueue);
        
        $jobData = $redisQueue->pop(60);
                                        
        if ($jobData == null) {
           return false;
        }

        $jobData = $jobData->getJobData();

        return $jobData;
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