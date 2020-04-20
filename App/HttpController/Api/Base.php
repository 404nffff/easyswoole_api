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

abstract class Base extends Controller
{
    protected function onRequest(?string $action): ?bool
    {
        // var_dump(123);
        //$cookie = $this->request()->getCookieParams('user_cookie');
        //对cookie进行判断，比如在数据库或者是redis缓存中，存在该cookie信息，说明用户登录成功
        $isLogin = true;
        if(!$isLogin){
            //这一步可以给前端响应数据，告知前端未登录
            $this->writeJson(401,null,'请先登录');
            //返回false表示不继续往下执行控制器action
            return  false;
        }

        //返回true表示继续往下执行控制器action
        return  true;
    }

    public function setToken() 
    {
        $jwtObject = Jwt::getInstance()
            ->setSecretKey('easyswoole') // 秘钥
            ->publish();

        $jwtObject->setAlg('HMACSHA256'); // 加密方式
        $jwtObject->setAud('user'); // 用户
        $jwtObject->setExp(time()+3600); // 过期时间
        $jwtObject->setIat(time()); // 发布时间
        $jwtObject->setIss('easyswoole'); // 发行人
        $jwtObject->setJti(md5(time())); // jwt id 用于标识该jwt
        $jwtObject->setNbf(time()+60*5); // 在此之前不可用
        $jwtObject->setSub('主题'); // 主题

        // 自定义数据
        $jwtObject->setData([
            'other_info'
        ]);

        // 最终生成的token
        $token = $jwtObject->__toString();

        $this->writeJson(200, $token ,'请先登录');
    }

    private function checkGoten() 
    {
        $jwtObject = Jwt::getInstance()->decode($token);

        $status = $jwtObject->getStatus();

        // 如果encode设置了秘钥,decode 的时候要指定
        // $status = $jwt->setSecretKey('easyswoole')->decode($token)

        switch ($status)
        {
            case  1:
                echo '验证通过';
                $jwtObject->getAlg();
                $jwtObject->getAud();
                $jwtObject->getData();
                $jwtObject->getExp();
                $jwtObject->getIat();
                $jwtObject->getIss();
                $jwtObject->getNbf();
                $jwtObject->getJti();
                $jwtObject->getSub();
                $jwtObject->getSignature();
                $jwtObject->getProperty('alg');
                break;
            case  -1:
                echo '无效';
                break;
            case  -2:
                echo 'token过期';
            break;
        }
    }
}