<?php

/**
 * Api基类，请求拦截
 * 
 * Class OnlineUser
 * @package App\Storage
 */
namespace App\HttpController\Api;

use EasySwoole\Http\AbstractInterface\Controller;

use EasySwoole\Jwt\Jwt;

use App\Storage\RedisSortedSets;


use EasySwoole\EasySwoole\Config as GlobalConfig;

abstract class Base extends Controller
{
    protected function onRequest(?string $action): ?bool
    {
        if($action == 'tokenCreate') {
            return true;
        }

        // var_dump(123);
        $isToken = $this->request()->hasHeader('token');
        //$cookie = $this->request()->getCookieParams('user_cookie');
        //对cookie进行判断，比如在数据库或者是redis缓存中，存在该cookie信息，说明用户登录成功
        if(!$isToken){
            //这一步可以给前端响应数据，告知前端未登录
            $this->writeJson(401,null,'请先登录');
            //返回false表示不继续往下执行控制器action
            return  false;
        }

        if(!$this->tokenCheck()){
            return false;
        }

        //返回true表示继续往下执行控制器action
        return  true;
    }

    /**
     * 发布token
     * 
     * @param string $user 用户
     * @param int    $expire 过期时间，（秒）
     * 
     * @return string | null.
     */
    private function tokenPub($user, $expire) 
    {
        

        $jwtObject = Jwt::getInstance()
            //->setSecretKey('live_api') // 秘钥
            ->publish();

        $jwtObject->setAlg('HMACSHA256'); // 加密方式
        $jwtObject->setAud($user); // 用户
        $jwtObject->setExp(time()+$expire); // 过期时间
        $jwtObject->setIat(time()); // 发布时间
        $jwtObject->setIss('live_api'); // 发行人
        $jwtObject->setJti(md5(time())); // jwt id 用于标识该jwt
        $jwtObject->setNbf(time()); // 在此之前不可用
        $jwtObject->setSub('api'); // 主题

        // // 自定义数据
        // $jwtObject->setData([
        //     'other_info'
        // ]);

        // 最终生成的token
        $token = $jwtObject->__toString();

        return $token;
    }

    /**
     * 生成token
     * 
     * @param string $user 用户
     * @param int    $expire 过期时间，（秒）
     * 
     * @return string | null.
     */
    public function tokenCreate() 
    {
        
        $redis = RedisSortedSets::getInstance('redis1', 'qiniu_jwt_token');
        $token = $this->tokenPub('qiniu', 3600*24*7);
        $redis->add(1, $token);
        $this->writeJson(200, $token ,'success');
    }

    /**
     * 验证token
     * 
     * @param string $user 用户
     * @param int    $expire 过期时间，（秒）
     * 
     * @return string | null.
     */
    protected function tokenCheck() 
    {
        $redis           = RedisSortedSets::getInstance('redis1', 'qiniu_jwt_token');
        $tokenArray      = $this->request()->getHeader('token');
        $token           = $tokenArray[0];
        $redisTokenArray = $redis->getAll();

        if(empty($redisTokenArray)){
            $this->writeJson(404, null,'无效');
            return false;
        }

        $redisToken = $redisTokenArray[0];

        if($token != $redisToken) {
            $this->writeJson(404, null,'无效');
            return false;
        }
        

        $jwtObject = Jwt::getInstance()->decode($token);
        $status    = $jwtObject->getStatus();

        // 如果encode设置了秘钥,decode 的时候要指定
        //$status = $jwtObject->setSecretKey('live_api')->decode($token);

        switch ($status)
        {
            case  1:
                //echo '验证通过';
                // $jwtObject->getAlg();
                //$user = $jwtObject->getAud();
                // $jwtObject->getData();
                // $jwtObject->getExp();
                // $jwtObject->getIat();
                // $jwtObject->getIss();
                // //$jwtObject->getNbf();
                // $jwtObject->getJti();
                // $jwtObject->getSub();
                // $jwtObject->getSignature();
                // $jwtObject->getProperty('alg');
                $expTime   = $jwtObject->getExp();
                $countTime = $expTime - time();
                $user      = $jwtObject->getAud();

                if($countTime < 3600*24) {
                    $redis->del($token);

                    $newToken = $this->tokenPub($user, 3600*24*7);
                    $redis->add(1, $newToken);
                    $this->response()->withHeader('token', $newToken);
                }
                return true;
                break;
            case  -1:
                //echo '无效';
                $this->writeJson(402,null,'无效');
                return false;
                break;
            case  -2:
                //echo 'token过期';
                $this->writeJson(403,null,'token过期');
                return false;
            break;
        }
    }

     /**
     * 推送到redis 
     * 
     * @param array $pushData 推送数据
     * 
     * @return string
     */
    protected function redisPush($pushData)
    {
        $redisName    = GlobalConfig::getInstance()->getConf('TASK_REDIS_DANMU_SEND_NAME');
        $redisChannel = GlobalConfig::getInstance()->getConf('REDIS_DANMU_SUB_CHANNEL_NAME');
        $redis        = \EasySwoole\Pool\Manager::getInstance()->get($redisName)->getObj();

        $jsonData = json_encode($pushData);

        $redis->publish($redisChannel, $jsonData);

        \EasySwoole\Pool\Manager::getInstance()->get($redisName)->recycleObj($redis);
    }

}