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
            
            $collector->post('/qiniu/tokenCreate','Api/Qiniu/tokenCreate');
            $collector->post('/qiniu/streamCreate','Api/Qiniu/streamCreate');
            $collector->post('/qiniu/streamInfoGet','Api/Qiniu/streamInfoGet');
            $collector->post('/qiniu/streamAllGet','Api/Qiniu/streamAllGet');
            $collector->post('/qiniu/streamLiveAllGet','Api/Qiniu/streamLiveAllGet');
            $collector->post('/qiniu/streamLiveStatusGet','Api/Qiniu/streamLiveStatusGet');
            $collector->post('/qiniu/streamLiveStatusBatchGet','Api/Qiniu/streamLiveStatusBatchGet');
            $collector->post('/qiniu/streamDisabled','Api/Qiniu/streamDisabled');
            $collector->post('/qiniu/streamEnabled','Api/Qiniu/streamEnabled');
            $collector->post('/qiniu/liveSave','Api/Qiniu/liveSave');
            $collector->post('/qiniu/liveSaveAs','Api/Qiniu/liveSaveAs');
            $collector->post('/qiniu/liveHistoryActivity','Api/Qiniu/liveHistoryActivity');
            $collector->post('/qiniu/liveSnapShot','Api/Qiniu/liveSnapShot');
            $collector->post('/qiniu/liveUpdateConverts','Api/Qiniu/liveUpdateConverts');
            $collector->post('/qiniu/rtmpPushUrlCreate','Api/Qiniu/rtmpPushUrlCreate');
            $collector->post('/qiniu/rtmpPlayUrlGet','Api/Qiniu/rtmpPlayUrlGet');
            $collector->post('/qiniu/hlsPlayUrlGet','Api/Qiniu/hlsPlayUrlGet');
            $collector->post('/qiniu/hdlPlayUrlGet','Api/Qiniu/hdlPlayUrlGet');
            $collector->post('/qiniu/snapShotPlayUrlGet','Api/Qiniu/snapShotPlayUrlGet');
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