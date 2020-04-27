<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

use App\Models\Base;
use App\Models\UserAccountd;
/**
 *  foke / 用户表
 * Class Player
 */
class User extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName     = 't_user';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';

    /**
     * 修改直播奖励金额
     * 
     * @param int    $orderId 订单号
     * @param int    $uid     user's id
     * @param number $account 金额
     * @param number $type    类型 5 直播红包 6 直播送礼
     *
     * @return false | array.
     */
    public function playerAmountUpdate($orderId, $uid, $account, $type)
    {
        $userData =  $this->get($uid);

        if(!$userData) {
            return false;
        }

        $userAccount  = $userData['player_amount'] + $account;

        $updateStatus = $userData->update(
            //update
            [
            'player_amount' => $userAccount,
            ], 
            //where
            [
                'id' => $uid
            ]
        );

        if(!$updateStatus){
            return false;
        }
        
        if(!UserAccountd::create()->LiveAccountLog($orderId, $uid, $account, $type)) {
            return false;
        };
        
        return true;
    }
}