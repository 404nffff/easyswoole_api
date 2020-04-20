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

    //七牛 直播api对接使用 AccessKey
    'QINIU_ACCESS_KEY' => '',

    //七牛 直播api对接使用 SecretKey
    'QINIU_SECRET_KEY' => '',
    
    //七牛 直播api对接使用 域名
    'QINIU_HTTP_DOMAIN' => '',
    
    //七牛 直播api对接使用 直播空间
    'QINIU_HUB' => ''

];
