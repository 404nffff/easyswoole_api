<?php


namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\ServerManager;

use App\Task\DanmuPushRedisQueueTask;
use App\Task\DanmuBroadCastTask;
use App\Task\MysqlPullDanmuRedisQueueTask;
use EasySwoole\EasySwoole\Config as GlobalConfig;
use App\Storage\OnlineUser;


class Index extends Base
{

    /**
     * 广播及时消息到所有用户在线用户
     * 
     * @return json | null.
     */
    public function broadCast()
    {
        
    }


     /**
     * 获取在线用户列表
     * 
     * @return json | null.
     */
    public function getOnlineUserList()
    {

        $server   = ServerManager::getInstance()->getSwooleServer();
        $userList = ['userNum' => 0]; 
        foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
            $connection = $server->connection_info($userFd);
            
            if ($connection['websocket_status'] == 3) {  // 用户正常在线时
                $userList['userNum']           = $userList['userNum']+1;
                $userList['userList'][$userFd] = $userInfo;
            }
        }

        if(empty($userList)) {
            $userList = null;
        }

        $this->writeJson(200, $userList, 'success');
    }


     /**
     * 查询在线用户
     * 
     * @return json | null.
     */
    function getOneOnlineUser()
    {
        $fd       = $this->request()->getRequestParam('fd');
        $username = $this->request()->getRequestParam('username');


        $server   = ServerManager::getInstance()->getSwooleServer();

        $userList = []; 
        if($fd != null && is_numeric($fd)) {

            foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
                if($fd == $userFd) {
                    $connection = $server->connection_info($userFd);
                    if ($connection['websocket_status'] == 3) {  // 用户正常在线时
                        $userList['userList'] = $userInfo;
                    }
                    break;
                };
            }
        }

        if($username != null) {

            foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
                $connection = $server->connection_info($userFd);
                if ($connection['websocket_status'] == 3) {  // 用户正常在线时
                    if($userInfo['username'] == $username) {
                        $userList['userList'] = $userInfo;
                        break;
                    }
                }
                
            }
        }

        if(empty($userList)) {
            $userList = null;
        }

        $this->writeJson(200, $userList, 'success');
    }

    /**
     * 指定用户发送一条及时消息
     * 
     * @return json | null.
     */
    function pushOneUserClient()
    {
        $fd       = $this->request()->getRequestParam('fd');
        $isLog    = $this->request()->getRequestParam('isLog');
        $content  = htmlspecialchars($this->request()->getRequestParam('content'));
        $username = $this->request()->getRequestParam('username');

        $server   = ServerManager::getInstance()->getSwooleServer();
        $task     = TaskManager::getInstance();

        $userList = []; 
        if($fd != null && is_numeric($fd) && $content != null) {

            foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
                if($fd == $userFd) {
                    $connection = $server->connection_info($userFd);
                    if ($connection['websocket_status'] == 3) {  // 用户正常在线时
                        $userList['userList'] = $userInfo;
                        $server->push($fd, $content);
                    }
                    break;
                };
            }
        }

        if($username != null) {

            foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
                $connection = $server->connection_info($userFd);
                if ($connection['websocket_status'] == 3) {  // 用户正常在线时
                    if($userInfo['username'] == $username) {
                        $userList['userList'] = $userInfo;
                        $server->push($userFd, $content);
                        break;
                    }
                }
                
            }
        }


        if(empty($userList)) {
            $userList = null;
            $this->writeJson(402, $userList, 'push error,this user is unline');
        }

        if($isLog == 1) {
            $data = ['content' => $content];

            $danmuPushRedisQueueTaskStatus = $task->async(new DanmuPushRedisQueueTask($data), function () {
                return true;
            });
    
            if($danmuPushRedisQueueTaskStatus < 0) {
                $this->writeJson(401, null, 'danmu push redis queue task error');
            }
        }


        $this->writeJson(200, $userList, 'success');
    }

}