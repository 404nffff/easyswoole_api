<?php
/**
 * 七牛云 api
 * 
 * Class Qiniu
 */
namespace App\HttpController\Api;

use App\HttpController\Api\Base;
use EasySwoole\EasySwoole\Config as GlobalConfig;


//导入七牛云
require join(DIRECTORY_SEPARATOR, array(dirname(dirname(dirname(__FILE__))), 'Utils/Qiniu', 'Pili_v2.php'));

class Qiniu extends Base
{
    
    //七牛云 客户端
    private $client;

    //七牛云 域名
    private $domain;

    //七牛云 空间名称
    private $hubName;

    //七牛云 直播空间
    private $hub;

    //七牛云 流
    private $stream;

    //七牛云 accessKey
    private $accessKey;

    //七牛云 secertKey
    private $secertKey;


     /**
     * 初始化
     * 
     * @return void.
     */
    public function __construct()
    {
        $this->accessKey = GlobalConfig::getInstance()->getConf('QINIU_ACCESS_KEY');
        $this->secertKey = GlobalConfig::getInstance()->getConf('QINIU_SECRET_KEY');
        $this->domain    = GlobalConfig::getInstance()->getConf('QINIU_HTTP_DOMAIN');
        $this->hubName   = GlobalConfig::getInstance()->getConf('QINIU_HUB');

        $mac           = new \Qiniu\Pili\Mac($this->accessKey, $this->secertKey);
        $this->client  = new \Qiniu\Pili\Client($mac);
        $this->hub     = $this->client->hub($this->hubName);
        
        parent::__construct();
    }


     /**
     * 响应格式化处理
     * 
     * @param string $response 响应数据.
     * 
     * @return void.
     */
    private function responseParser($response)
    {
        preg_match('/httpcode:(\d+)/', $response, $arr);
        preg_match('/message{"error":"(.*?)"}/', $response, $arr2);
        $httpCode = isset($arr[1])?$arr[1]:999;
        $message  = isset($arr2[1])?$arr2[1]:$response;

        // if(!isset($httpCode) && !isset($message)) {
        //     $httpCode = '999';
        //     $message  = $response;
        // }

        return ['code' => $httpCode, 'msg' => $message];
    }


     /**
     * 异常类捕获
     * 
     * @param throwable $throwable 流名称.
     * 
     * @return void.
     */
    public function onException(\Throwable $throwable): void
    {
        $responseArr = $this->responseParser($throwable->getMessage());
        $this->writeJson($responseArr['code'], null, $responseArr['msg']);
    }

     /**
     * 流名称检验规则
     * 
     * @param string $streamKey 流名称.
     * 
     * @return void.
     */
    private function streamKeyValiData($streamKey)
    {
        if(strlen($streamKey) < 4 || strlen($streamKey) > 200) {
            $this->writeJson('400', null, '名称必须满足 4-200个数字或字母');
            return false;
        }

        if(!preg_match('/[a-zA-Z\d]+/', $streamKey)){
            $this->writeJson('400', null, '名称必须满足 4-200个数字或字母');
            return false;
        }
        return true;
    }

     /**
     * 获取流 对象
     * 
     * @param string $streamKey 流名称.
     * 
     * @return void.
     */
    public function streamObjCreate($streamKey)
    {
        $this->stream = $this->hub->stream($streamKey);
    }


     /**
     * 创建流
     * 
     * @param request $streamKey 流名称.
     * 
     * @return void.
     */
    public function streamCreate()
    {
        $streamKey   = $this->request()->getRequestParam('streamKey');

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        try {
           $this->hub->create($streamKey);

           $this->writeJson('200', null, '创建成功');

        } catch (\Exception $e) {
            $this->onException($e);
        }        
    }


    /**
     * 获取流信息
     * 
     * @param request $streamKey 流名称.
     * 
     * @return json.
     */
    public function streamInfoGet()
    {

        $streamKey = $this->request()->getRequestParam('streamKey');
        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);

        try {
            $resp = $this->stream->info();
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
       
    }


    /**
     * 获取所有流信息
     * 
     * PARAM
     * @param request prefix 流名的前缀.
     * @param request limit  限定了一次最多可以返回的流个数, 实际返回的流个数可能小于这个 limit 值.
     * @param request marker 是上一次遍历得到的流标.
     * 
     * RETURN
     * @return keys    流名的数组.
     * @return omarker 记录了此次遍历到的游标, 在下次请求时应该带上, 如果 omarker 为 "" 表示已经遍历完所有流.
     */
    public function streamAllGet()
    {

        $prefix = $this->request()->getRequestParam('prefix');
        $limit  = $this->request()->getRequestParam('limit');
        $marker = $this->request()->getRequestParam('marker');

        try {
            $resp = $this->hub->listStreams($prefix, $limit, $marker);
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }  
    }


    /**
     * 列出正在直播的流
     * 
     * PARAM
     * @param request prefix 流名的前缀.
     * @param request limit  限定了一次最多可以返回的流个数, 实际返回的流个数可能小于这个 limit 值.
     * @param request marker 是上一次遍历得到的流标.
     * 
     * RETURN
     * @return keys    流名的数组.
     * @return omarker 记录了此次遍历到的游标, 在下次请求时应该带上, 如果 omarker 为 "" 表示已经遍历完所有流.
     */
    public function streamLiveAllGet()
    {
        $prefix = $this->request()->getRequestParam('prefix');
        $limit  = $this->request()->getRequestParam('limit');
        $marker = $this->request()->getRequestParam('marker');

        try {
            $resp = $this->hub->listLiveStreams($prefix, $limit, $marker);
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }  
    }

    /**
     * 获取流直播状态
     * 
     * @param request $streamKey 流名称.
     * 
     * @return json
     */
    public function streamLiveStatusGet()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);

        try {
            $resp = $this->stream->liveStatus();
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 批量获取流直播状态
     * 
     * @param request $streamKey 流名称.
     * 
     * @return json
     */
    public function streamLiveStatusBatchGet()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        
        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }
        $streamKeyArray = json_decode($streamKey, true);

        if(!is_array($streamKeyArray)){
            $this->writeJson('401', $resp, '格式有错误');
        }

        $this->streamObjCreate($streamKey);

        try {
            $resp = $this->hub->batchLiveStatus($streamKeyArray);
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 禁用流
     * 
     * @param request $streamKey    流名称.
     * @param request $disabledTill Unix 时间戳, 在这之前流均不可用。不填则默认为 -1（永久禁播）.
     * 
     * @return json 
     */
    public function streamDisabled()
    {
        $streamKey    = $this->request()->getRequestParam('streamKey');
        $disabledTill = $this->request()->getRequestParam('disabledTill');
        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);
        try {
            //禁用流           
            $this->stream->disable($disabledTill);
            $this->writeJson('200', null, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 禁用流
     * 
     * @param request $streamKey    流名称.
     * 
     * @return json
     */
    public function streamEnabled()
    {
        $streamKey    = $this->request()->getRequestParam('streamKey');
        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);
        try {
            //启用流           
            $this->stream->enable();
            $this->writeJson('200', null, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 保存直播
     * 
     * @param request $streamKey 流名称.
     * @param request $start:    Unix 时间戳, 起始时间, 0 值表示不指定, 则不限制起始时间.
     * @param request $end       Unix 时间戳, 结束时间, 0 值表示当前时间.
     * 
     * @return fname: 保存到bucket里的文件名, 由系统生成.
     */
    public function liveSave()
    {
        
        $startTime = $this->request()->getRequestParam('start');
        $endTime   = $this->request()->getRequestParam('end');
        $streamKey = $this->request()->getRequestParam('streamKey');
        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }
        if(!is_numeric($startTime) || !is_numeric($endTime)){
            $this->writeJson('401', null, '格式有错误');
            return false;
        }

        $this->streamObjCreate($streamKey);
        try {
            $fname = $this->stream->save($startTime, $endTime);
            $this->writeJson('200',  $fname, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 灵活度更高的保存直播回放
     * 
     * @param request $streamKey 流名称.
     * @param request $option    选项 json.
     *
     * 
     * $option 选项
     *
     * @param fname: 保存的文件名, 不指定会随机生成.
     * @param start: Unix 时间戳, 起始时间, 0 值表示不指定, 则不限制起始时间.
     * @param end: Unix 时间戳, 结束时间, 0 值表示当前时间.
     * @param format: 保存的文件格式, 默认为m3u8.
     * @param pipeline: dora 的私有队列, 不指定则用默认队列.
     * @param notify: 保存成功后的回调地址.
     * @param expireDays: 对应ts文件的过期时间.
     *   -1 表示不修改ts文件的expire属性.
     *   0  表示修改ts文件生命周期为永久保存.
     *   >0 表示修改ts文件的的生命周期为ExpireDays.
     * 
     * @return fname: 保存到bucket里的文件名.
     * @return persistentID: 异步模式时，持久化异步处理任务ID，通常用不到该字段.

     * @return json
     */
    public function liveSaveAs()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        $option    = $this->request()->getRequestParam('option');

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);

        $optionArray = json_decode($option, true);

        if(!is_array($optionArray)){
            $this->writeJson('401', null, '格式有错误');
            return false;
        }
        
        try {
            $resp = $this->stream->saveas($optionArray);
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 查询推流历史
     * 
     * @param request $streamKey 流名称.
     * @param request $start     Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.
     * @param request $end       Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.
     *
     * RETURN
     * @return items: 数组. 每个item包含一次推流的开始及结束时间.
     *   @start: Unix 时间戳, 直播开始时间.
     *   @end: Unix 时间戳, 直播结束时间.
     */
    public function liveHistoryActivity()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        $start     = $this->request()->getRequestParam('start');
        $end       = $this->request()->getRequestParam('end');

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);

        try {
            $resp = $this->stream->historyActivity($start, $end);
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 保存直播截图
     * 
     * @param request $streamKey 流名称.
     * @param request $start     Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.
     * @param request $end       Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.
     *  
     * $option 选项
     * 
     * @param fname: 保存的文件名, 不指定会随机生成.
     * @param time: Unix 时间戳, 保存的时间点, 默认为当前时间.
     * @param format: 保存的文件格式, 默认为jpg.
     * 
     * RETURN
     * @param fname: 保存到bucket里的文件名.
     */
    public function liveSnapShot()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        $option    = $this->request()->getRequestParam('option');


        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);


        $optionArray = json_decode($option, true);

        if(!is_array($optionArray)){
            $this->writeJson('401', null, '格式有错误');
            return false;
        }


        try {
            $resp = $this->stream->snapshot($optionArray);
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }

    
    /**
     * 更改流的实时转码规格
     * 
     * @param request $streamKey 流名称.
     * @param request $option    选项 json. array("480p", "720p")
     */
    public function liveUpdateConverts()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        $option    = $this->request()->getRequestParam('option');


        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);


        $optionArray = json_decode($option, true);

        if(!is_array($optionArray)){
            $this->writeJson('401', null, '格式有错误');
            return false;
        }


        try {
            $resp = $this->stream->updateConverts($optionArray);
            $this->writeJson('200', $resp, 'success');
        } catch (\Exception $e) {
            $this->onException($e);
        }
    }


    /**
     * 生成 RTMP 推流地址
     * 
     * @param request $streamKey          流名称.
     * @param request $expireAfterSeconds 表示 URL 在多久之后失效 (秒).
     * 
     * @return json
     */
    public function rtmpPushUrlCreate()
    {
        $streamKey          = $this->request()->getRequestParam('streamKey');
        $expireAfterSeconds = $this->request()->getRequestParam('expire');
        

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);

        
        if(!is_numeric($expireAfterSeconds) && $expireAfterSeconds <= 0){
            $this->writeJson('401', null, '格式有错误');
            return false;
        }


        $url = \Qiniu\Pili\RTMPPublishURL("pili-publish-rtmp.".$this->domain, $this->hubName, $streamKey, $expireAfterSeconds, $this->accessKey, $this->secertKey);
       
        $this->writeJson('200', $url, 'success');
    }

    
    /**
     * RTMP 直播放址
     * 
     * @param request $streamKey 流名称.
     * 
     * @return json
     */
    public function rtmpPlayUrlGet()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);

        $url = \Qiniu\Pili\RTMPPlayURL("pili-live-rtmp.".$this->domain, $this->hubName, $streamKey);
       
        $this->writeJson('200', $url, 'success');
    }

    
    /**
     * HLS 直播地址
     * 
     * @param request $streamKey 流名称.
     * 
     * @return json
     */
    public function hlsPlayUrlGet()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);

        $url = \Qiniu\Pili\HLSPlayURL("pili-live-hls.".$this->domain, $this->hubName, $streamKey);
       
        $this->writeJson('200', $url, 'success');
    }


    /**
     * HDL 直播地址
     * 
     * @param request $streamKey 流名称.
     * 
     * @return json
     */
    public function hdlPlayUrlGet()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);
        $url =  \Qiniu\Pili\HDLPlayUR("pili-live-hdl.".$this->domain, $this->hubName, $streamKey);
       
        $this->writeJson('200', $url, 'success');
    }


    /**
     * 截图直播地址
     * 
     * @param request $streamKey 流名称.
     * 
     * @return json
     */
    public function snapShotPlayUrlGet()
    {
        $streamKey = $this->request()->getRequestParam('streamKey');
        

        if(!$this->streamKeyValiData($streamKey)) {
            return false;
        }

        $this->streamObjCreate($streamKey);
        $url =  \Qiniu\Pili\SnapshotPlayURL("pili-live-snapshot".$this->domain, $this->hubName, $streamKey);
       
        $this->writeJson('200', $url, 'success');
    }



}