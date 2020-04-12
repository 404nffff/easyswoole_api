<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SOCKET_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 3,
            'reload_async' => true,
            'max_wait_time'=>3
        ],
        'TASK'=>[
            'workerNum'=>3,
            'maxRunningNum'=>128,
            'timeout'=>15
        ]
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,


    'MYSQL' => [
        'host'          => '127.0.0.1',
        'port'          => '3306',
        'user'          => 'root',
        'timeout'       => '5',
        'charset'       => 'utf8mb4',
        'password'      => 'root',
        'database'      => 'danmu',
        'POOL_MAX_NUM'  => '20',
        'POOL_TIME_OUT' => '0.1',
    ],
    

    //发布 / 订阅消息 配置
    'REDIS1' => [
        'host'          => '127.0.0.1',
        'port'          => '6379',
        'auth'          => '',
        'POOL_MAX_NUM'  => '20',
        'POOL_MIN_NUM'  => '5',
        'POOL_TIME_OUT' => '0.1'
        
    ],
    //REDIS1服务注册名称
    'REDIS_REGISTER_NAME_1' => 'redis1',


    //消息队列配置
    'REDIS2' => [
        'host'          => '127.0.0.1',
        'port'          => '6380',
        'auth'          => '',
        'POOL_MAX_NUM'  => '20',
        'POOL_MIN_NUM'  => '5',
        'POOL_TIME_OUT' => '0.1'
        
    ],

    //REDIS2服务注册名称
    'REDIS_REGISTER_NAME_2' => 'redis2',


    //人员集合名称
    'REDIS_SORT_SET_NAME' => 'danmu',
    
    //消息列队名称
    'REDIS_QUEUE_NAME'   => 'danmuQueue',


    //TASK\DanmuBroadCastTask 使用注册Redis 名称
    'TASK_REDIS_DANMU_SEND_NAME'  => 'redis1',

    //TASK\DanmuPushRedisQueueTask , Process\RedisQueueMysqlProcess  使用注册Redis 名称
    'TASK_REDIS_DANMU_QUEUE_NAME' => 'redis2',


    //RedisQueueMysqlProcess 自定义进程定时器时间（毫秒），到时间自动执行
    'PROCESS_REDIS_QUEUE_MYSQL_TIME' => 5*1000,


    //在线用户表 swoole table 名称
    'SWOOLE_TABLE_ONLINE_USE_NAME' => 'onlineUsers'

    // 'REDIS_CLUSTER' => [
    //     ['172.16.253.156', 9001],
    //     ['172.16.253.156', 9002],
    //     ['172.16.253.156', 9003],
    //     ['172.16.253.156', 9004]
    // ]

];
