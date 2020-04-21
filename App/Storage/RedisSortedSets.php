<?php 
/**
 * Redis 有序合集记录fd.
 * User: w
 * Date: 2020-04-09
 * Time: 9:19
 */

namespace App\Storage;


use EasySwoole\Redis\Redis as Connection;
use EasySwoole\RedisPool\RedisPool;

use \EasySwoole\Pool\Manager;

use EasySwoole\Component\Singleton;

class RedisSortedSets 
{

    //单例
    use Singleton;

    //连接池
    protected $pool;

    //连接池对象
    protected $redis;

    //客户端id
    private $client;


    //reid 存储名称
    protected $fdName;

     /**
     * 初始化
     * @param string $pool   连接配置名称.
     * @param string $fdName 名称.
     */
    public function __construct($redisConfigName, $fdName)
    {
        $this->pool   = \EasySwoole\Pool\Manager::getInstance()->get($redisConfigName);
        $this->fdName = $fdName;

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
     * 往有序合集记录添加.
     * 
     * @param int $score 分数.
     * @param int $fd    fd.
     * 
     * @return bool.
     */
    public function add($score, $fd): bool
    {
        $result = $this->pool->getObj()->zAdd($this->fdName, $score, $fd);
        return $result;
    }


    /**
     * 获取长度
     * 
     * @return int | null.
     */
    public function count(): ?int
    {
        $result = $this->pool->getObj()->zCard($this->fdName);
        return $result;
    }


    /**
     * 删除
    * 
     * @param int $fd fd id.
     * 
     * @return int | null.
     */
    public function del($fd): ?int
    {
        // $status = $this->getOne($fd);
        // if (!$status || $status == null) {
        //     return true;
        // }
        $result = $this->pool->getObj()->zRem($this->fdName, $fd);
        return $result;
    }


    /**
     * 获取一条
     * 
     * @param int $fd fd id.
     * 
     * @return int | null.
     */
    public function getOne($fd): ?int
    {
        $result = $this->pool->getObj()->zRank($this->fdName, $fd);
        return $result;
    }


    /**
     * 获取所有成员
     * 
     * @param int $fd fd id.
     * 
     * @return array.
     */
    public function getAll(): ?array
    {
        $result = $this->pool->getObj()->zRange($this->fdName, 1, -1);
        return $result;
    }


    /**
     * 清空成员
     * 
     * @return int | null.
     */
    public function clearAll(): ?int
    {
        $result = $this->pool->getObj()->zRemRangeByScore($this->fdName, 1, 999);
        return $result;
    }

}