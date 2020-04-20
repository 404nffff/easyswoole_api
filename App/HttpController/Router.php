<?php
/**
 * 路由初始类.
 * User: w
 * Date: 2020/04/13
 * Time: 上午10:39
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class Router extends AbstractRouter
{
    /**
     * 初始化
     * @param RouteCollector $routeCollector.
     * 
     * @return void.
     */
    public function initialize(RouteCollector $routeCollector)
    {
        //全局模式拦截
        $this->setGlobalMode(true);

        $routeCollector->get('/','Index/index');
        $routeCollector->get('/streamCreate','Index/streamCreate');
        $routeCollector->get('/streamInfoGet','Index/streamInfoGet');
        $routeCollector->get('/streamAllGet','Index/streamAllGet');
        $routeCollector->get('/streamLiveAllGet','Index/streamLiveAllGet');
        $routeCollector->get('/streamLiveStatusGet','Index/streamLiveStatusGet');
        $routeCollector->get('/streamLiveStatusBatchGet','Index/streamLiveStatusBatchGet');
        $routeCollector->get('/streamDisabled','Index/streamDisabled');
        $routeCollector->get('/streamEnabled','Index/streamEnabled');
        $routeCollector->get('/liveSave','Index/liveSave');
        $routeCollector->get('/liveSaveAs','Index/liveSaveAs');
        $routeCollector->get('/liveHistoryActivity','Index/liveHistoryActivity');
        $routeCollector->get('/liveSnapShot','Index/liveSnapShot');

        $routeCollector->addGroup('/api',function (RouteCollector $collector){
            $collector->get('/broadCast','Api/Index/broadCast');
            $collector->get('/getOnlineUserList','Api/Index/getOnlineUserList');
            $collector->get('/getOneOnlineUser','Api/Index/getOneOnlineUser');
            $collector->get('/pushOneUserClient','Api/Index/pushOneUserClient');
        });

        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
            $response->write('未找到处理方法');
            return false;//结束此次响应
        });

        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
            $response->write('404 not found');
            return '/';//重定向到index路由
        });

    }
}