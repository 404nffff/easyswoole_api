<?php
namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Http\Message\Status;


//加载默认配置
use EasySwoole\EasySwoole\Config as GlobalConfig;

// redis
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;
use EasySwoole\RedisPool\RedisPool;
use Swoole\Coroutine\Scheduler;
use Swoole\Timer as SwooleTimer;


//ORM
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\Db\Config;


//swoole table
use EasySwoole\Component\TableManager;
use Swoole\Table;



class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');


        $confInstance = GlobalConfig::getInstance();

        //redis注册
        $config = new \EasySwoole\Pool\Config();
        $redisConfig1 = new RedisConfig($confInstance->getConf('REDIS1'));
        $redisConfig2 = new RedisConfig($confInstance->getConf('REDIS2'));
        \EasySwoole\Pool\Manager::getInstance()->register(
            new \App\Pool\RedisPool($config, $redisConfig1), 
                $confInstance->getConf('REDIS_REGISTER_NAME_1')
        );
        \EasySwoole\Pool\Manager::getInstance()->register(
            new \App\Pool\RedisPool($config, $redisConfig2), 
            $confInstance->getConf('REDIS_REGISTER_NAME_2')
        );


        //注册redis订阅服务
        Redis::getInstance()->register('waf', $redisConfig1);
        $scheduler = new Scheduler();
        $scheduler->add(function ()use($config) {

          
            Redis::invoke('waf',function ($client){
                $redisFdName = GlobalConfig::getInstance()->getConf('REDIS_SORT_SET_NAME');

                //$client->zRemRangeByScore($redisFdName, 1, 999);

                //$client->zAdd($redisFdName, 0, 0);
            });
            Redis::defer('waf');
            Redis::getInstance()->get('waf')->reset();
        });
        $scheduler->start();
        SwooleTimer::clearAll();


        //注册MYSQL
        $config = new Config(GlobalConfig::getInstance()->getConf("MYSQL"));
        DbManager::getInstance()->addConnection(new Connection($config));
    }

    public static function mainServerCreate(EventRegister $register)
    {
        
        // //注册swoole table
        // $tableName = GlobalConfig::getInstance()->getConf('SWOOLE_TABLE_ONLINE_USE_NAME');
        // TableManager::getInstance()->add($tableName, [
        //     'fd' => ['type' => Table::TYPE_INT, 'size' => 8],
        //     'avatar' => ['type' => Table::TYPE_STRING, 'size' => 128],
        //     'username' => ['type' => Table::TYPE_STRING, 'size' => 128],
        //     'last_heartbeat' => ['type' => Table::TYPE_INT, 'size' => 4],
        // ]);


        $register->add($register::onWorkerStart,function (){
            //链接预热
            DbManager::getInstance()->getConnection()->getClientPool()->keepMin();
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}