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
     

        //七牛云
        $routeCollector->addGroup('/api/qiniu',function (RouteCollector $collector){
            
            $collector->post('/tokenCreate','Api/Qiniu/tokenCreate');
            $collector->post('/streamCreate','Api/Qiniu/streamCreate');
            $collector->post('/streamInfoGet','Api/Qiniu/streamInfoGet');
            $collector->post('/streamAllGet','Api/Qiniu/streamAllGet');
            $collector->post('/streamLiveAllGet','Api/Qiniu/streamLiveAllGet');
            $collector->post('/streamLiveStatusGet','Api/Qiniu/streamLiveStatusGet');
            $collector->post('/streamLiveStatusBatchGet','Api/Qiniu/streamLiveStatusBatchGet');
            $collector->post('/streamDisabled','Api/Qiniu/streamDisabled');
            $collector->post('/streamEnabled','Api/Qiniu/streamEnabled');
            $collector->post('/liveSave','Api/Qiniu/liveSave');
            $collector->post('/liveSaveAs','Api/Qiniu/liveSaveAs');
            $collector->post('/liveHistoryActivity','Api/Qiniu/liveHistoryActivity');
            $collector->post('/liveSnapShot','Api/Qiniu/liveSnapShot');
            $collector->post('/liveUpdateConverts','Api/Qiniu/liveUpdateConverts');
            $collector->post('/rtmpPushUrlCreate','Api/Qiniu/rtmpPushUrlCreate');
            $collector->post('/rtmpPlayUrlGet','Api/Qiniu/rtmpPlayUrlGet');
            $collector->post('/hlsPlayUrlGet','Api/Qiniu/hlsPlayUrlGet');
            $collector->post('/hdlPlayUrlGet','Api/Qiniu/hdlPlayUrlGet');
            $collector->post('/snapShotPlayUrlGet','Api/Qiniu/snapShotPlayUrlGet');
        });


        //抽奖红包
        $routeCollector->addGroup('/api/lottery',function (RouteCollector $collector){
            
            $collector->post('/redBoxCreate','Api/Lottery/redBoxCreate');
            $collector->post('/redBoxGet','Api/Lottery/redBoxGet');
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