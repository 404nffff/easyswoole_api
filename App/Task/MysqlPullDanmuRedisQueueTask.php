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

use App\Models\DanmuList;

use EasySwoole\ORM\DbManager;

/**
 * Mysql 拉弹幕redis队列
 * 
 * Class SendDanmuTask
 * @package App\Task
 */
class MysqlPullDanmuRedisQueueTask implements TaskInterface
{
    //队列名称
    protected $redisQueueName;

    //redis 注册名称
    protected $redisName;

    //接受内容
    protected $content;

    public function __construct($redisName, $redisQueueName)
    {
        $this->redisQueueName = $redisQueueName;
        $this->redisName      = $redisName;
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

        $redisQueue = new RedisQueue($this->redisName, $this->redisQueueName);
        
        //$timeout 过期时间
        $jobData = $redisQueue->pop(3);
        
        if ($jobData != null) {
            $data = $jobData->getJobData();

            $content = $data['content'];
            
            $this->content = $content;

         
            $danmu = new DanmuList();

            $client = DbManager::getInstance()->getConnection()->getClientPool();
            $value = DbManager::getInstance()->invoke(function ($client){

                $testUserModel = DanmuList::invoke($client);
                $testUserModel->content = $this->content;
                 $data = $testUserModel->save();
                return $testUserModel;
            });
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