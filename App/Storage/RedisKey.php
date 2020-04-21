<?php 
/**
 * Redis 键值记录.
 * User: w
 * Date: 2020-04-13
 * Time: 9:19
 */

namespace App\Storage;


use \EasySwoole\Pool\Manager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Component\Singleton;

class RedisKey
{

    //单例
    use Singleton;

    //连接池
    protected $pool;


     /**
     * 初始化
     * 
     * @param string $redisConfigName 连接配置名称.
     * 
     * @return void
     */
    public function __construct($redisConfigName)
    {
       
        $this->pool = \EasySwoole\Pool\Manager::getInstance()->get($redisConfigName);
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
     * 设置 key .
     * 
     * @param string $key   键名.
     * @param string $value 值.
     * @param int    $expore   过期时间（秒）.
     * 
     * @return bool.
     */
    public function set($key, $value, $expire = 0): bool
    {
        $result = $this->pool->getObj()->set($key, $value);
        if($expire != 0){
            $this->pool->getObj()->expire($key, $expire);
        }
        $this->recycle();
        return $result;
    }


    /**
     * 获取 key .
     * 
     * @param string $key    键名.
     * 
     * @return array | false.
     */
    public function get($key)
    {
        if(!$this->pool->getObj()->exists($key)){
            return false;
        }
        $result = $this->pool->getObj()->dump($key);
        $this->recycle();
        return $result;
    }


    /**
     * 删除 key .
     * 
     * @param string|array $key键名.
     * 
     * @return bool.
     */
    public function del($key)
    {
        $result = $this->pool->getObj()->unlink($key);
        $this->recycle();
        return $result;
    }

    /**
     * 匹配获取
     * 
     * @param string $key 匹配key.
     * 
     * @return array.
     */
    public function matchGet($key): ?array
    {
        $result = $this->pool->getObj()->keys($key);
        $this->recycle();
        return $result;
    }


    /**
     * 返回过期时间
     * 
     * @param string $key 匹配key.
     * 
     * @return array.
     */
    public function ttlGet($key): ?array
    {
        $result = $this->pool->getObj()->ttl($key);
        $this->recycle();
        return $result;
    }
}