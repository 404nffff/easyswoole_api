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
     

        $routeCollector->addGroup('/api',function (RouteCollector $collector){
            
            $collector->get('/streamCreate','Api/Qiniu/streamCreate');
            $collector->get('/streamInfoGet','Api/Qiniu/streamInfoGet');
            $collector->get('/streamAllGet','Api/Qiniu/streamAllGet');
            $collector->get('/streamLiveAllGet','Api/Qiniu/streamLiveAllGet');
            $collector->get('/streamLiveStatusGet','Api/Qiniu/streamLiveStatusGet');
            $collector->get('/streamLiveStatusBatchGet','Api/Qiniu/streamLiveStatusBatchGet');
            $collector->get('/streamDisabled','Api/Qiniu/streamDisabled');
            $collector->get('/streamEnabled','Api/Qiniu/streamEnabled');
            $collector->get('/liveSave','Api/Qiniu/liveSave');
            $collector->get('/liveSaveAs','Api/Qiniu/liveSaveAs');
            $collector->get('/liveHistoryActivity','Api/Qiniu/liveHistoryActivity');
            $collector->get('/liveSnapShot','Api/Qiniu/liveSnapShot');
            $collector->get('/liveUpdateConverts','Api/Qiniu/liveUpdateConverts');
            $collector->get('/rtmpPushUrlCreate','Api/Qiniu/rtmpPushUrlCreate');
            $collector->get('/rtmpPlayUrlGet','Api/Qiniu/rtmpPlayUrlGet');
            $collector->get('/hlsPlayUrlGet','Api/Qiniu/hlsPlayUrlGet');
            $collector->get('/hdlPlayUrlGet','Api/Qiniu/hdlPlayUrlGet');
            $collector->get('/snapShotPlayUrlGet','Api/Qiniu/snapShotPlayUrlGet');
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