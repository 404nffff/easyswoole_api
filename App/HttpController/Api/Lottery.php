<?php
/**
 * 抽奖红包推送
 * 
 * Class Qiniu
 */
namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\ServerManager;


use EasySwoole\EasySwoole\Config as GlobalConfig;

use App\Task\RedisQueuePushDataTask;
use App\Task\RedisQueuePullDataTask;
use App\Models\Player;
use App\Models\OtherUser;


class Lottery extends Base
{

    /**
     * 生成红包
     * 
     * @return json | null.
     */
    public function redBoxCreate()
    {

        // $mysql = \EasySwoole\Pool\Manager::getInstance()->get('mysql2')->getObj();

        //$player = new Player();

       
        
        // var_dump($mysql);
        $redTotalMoney = $this->request()->getRequestParam('redTotalMoney');
        $redNum        = $this->request()->getRequestParam('redNum');
        $unionid       = $this->request()->getRequestParam('unionid');
        $playerId      = $this->request()->getRequestParam('playerId');
        
        
        if(!is_numeric($redTotalMoney) || !is_numeric($redNum) || !is_numeric($playerId) || $unionid == '') {
            $this->writeJson('400', null, '参数错误');
            return false;
        }

        // $res = Player::create()->checkExistsById($playerId);

        // if(!$res) {
        //     $this->writeJson('401', null, '活动不存在');
        //     return false;
        // }

        
        // $res = OtherUser::create()->checkExistsById($playerId);

        // if(!$res) {
        //     $this->writeJson('401', null, '活动不存在');
        //     return false;
        // }

        go(function (){
            $csp = new \EasySwoole\Component\Csp();
            

            //推送弹幕到 Redis队列 协程异步
            $csp->add('t1',function (){
                
                $redTotalMoney = $this->request()->getRequestParam('redTotalMoney');
                $redNum        = $this->request()->getRequestParam('redNum');
                $unionid       = $this->request()->getRequestParam('unionid');
                $playerId      = $this->request()->getRequestParam('playerId');


                $redBoxArray   = $this->redEnvelopeRandomProduce($redTotalMoney, $redNum);
        
                
                $redisQueueNamePrefix = GlobalConfig::getInstance()->getConf('REDIS_LOTTERY_QUEUE_NAME_PREFIX');
                $redisName            = GlobalConfig::getInstance()->getConf('REDIS_LOTTERY_USE_NAME');
        
                $queueName = $redisQueueNamePrefix.$unionid.'_'.$playerId;
               

                foreach($redBoxArray as $value) {
                    $task = TaskManager::getInstance();
                    $task->async(new RedisQueuePushDataTask($value, $redisName, $queueName));
                }
        
                \co::sleep(1);
               return 't1 result';
            });
             $csp->exec();
        });

        // $this->redisPush(['action' => 'broadCast', 'content' => '123123', 'roomId' => 11, 'username' => 'server']);
    }


    /**
     * 获取红包
     * 
     * @return json | null.
     */
    public function redBoxGet()
    {

        
        // $data =  $task->sync(function (){
        //     echo "同步调用task1\n";
        //     return "可以返回调用结果\n";
        // });

        $redisQueueNamePrefix = GlobalConfig::getInstance()->getConf('REDIS_LOTTERY_QUEUE_NAME_PREFIX');
        $redisName            = GlobalConfig::getInstance()->getConf('REDIS_LOTTERY_USE_NAME');
        $queueName            = $redisQueueNamePrefix.'10_10';
        $task                 = TaskManager::getInstance();
        $result               = $task->sync(new RedisQueuePullDataTask($redisName, $queueName));
       
        $this->writeJson(200, null, $result);
    }


    /**
     * 拼手气红包生成算法
     * @param $red_total_money
     * @param $red_num
     * @return array
     */
    private function redEnvelopeRandomProduce($red_total_money, $red_num)
    {
        //1 声明定义最小红包值
        $red_min = 0.01;
    
        //2 声明定义生成红包结果
        $result_red = array();
    
        
        //3 惊喜红包计算
        for ($i = 1; $i < $red_num; $i++) {
            //3.1 计算安全上限 | 保证每个人都可以分到最小值
            $safe_total = ($red_total_money - ($red_num - $i) * $red_min) / ($red_num - $i);
            //3.2 随机取出一个数值
            $red_money_tmp = mt_rand($red_min * 100, $safe_total * 100) / 100;
            //3.3 将金额从红包总金额减去
            $red_total_money -= $red_money_tmp;
            $result_red[] = array(
                'red_code' => $i,
                'red_title' => '红包' . $i,
                'red_money' =>  round($red_money_tmp, 2),
            );
        }
    
        //4 最后一个红包
        $result_red[] = array(
            'red_code' => $red_num,
            'red_title' => '红包' . $red_num,
            'red_money' => round($red_total_money, 2),
        );
    
        return $result_red;
    }

}