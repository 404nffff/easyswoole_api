<?php 
/**
 * Redis连接池 队列服务.
 * User: w
 * Date: 2020-04-09
 * Time: 9:19
 */

namespace App\Storage;

use EasySwoole\Queue\Job;
use EasySwoole\Queue\QueueDriverInterface;
use EasySwoole\Redis\Redis as Connection;
use EasySwoole\RedisPool\RedisPool;

use \EasySwoole\Pool\Manager;

class RedisQueue  implements QueueDriverInterface
{

    //连接池配置
    protected $pool;

    //队列名称
    protected $queueName;

     /**
     * 初始化
     * @param String $redisConfigName 连接配置名称.
     * @param String $queueName       队列名称.
     */
    public function __construct($redisConfigName, string $queueName = 'EasySwoole')
    {
        $this->pool      = \EasySwoole\Pool\Manager::getInstance()->get($redisConfigName);
        $this->queueName = $queueName;
    }



    /**
     * 连接池对象回收
     * 
     * @return bool. 
     */
    public function recycle()
    {
        $getRedisObj = $this->pool->getObj();
        return $this->pool->recycleObj($getRedisObj);
    }


    /**
     * 往列队里面推.
     * @param Job $job jobData实例.
     * 
     * @return bool.
     */
    public function push(Job $job): bool
    {
        $data = json_encode($job->getJobData());

        
        return $this->pool->invoke(function (Connection $connection)use($data){

            return $connection->lPush($this->queueName, $data);

        });
    }

    /**
     * 从列队里面取
     * @param float $timeout 过期时间
     * 
     * @return Job | null.
     */
    public function pop(float $timeout = 3.0): ?Job
    {
        return $this->pool->invoke(function (Connection $connection){

            $redisData = $connection->rPop($this->queueName);

            if($redisData == null) {
                return null;
            }

            $data = json_decode($redisData, true);

           
            if(is_array($data)){
                $job = new Job();
                $job->setJobData($data);
                return $job;
            }else{
                return null;
            }

        });
    }


    /**
     * 获取长度
     * 
     * @return int | null.
     */
    public function size(): ?int
    {
        return $this->pool->invoke(function (Connection $connection){

            return $connection->lLen($this->queueName);

        });
    }

}