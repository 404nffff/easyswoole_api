<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;


use App\Models\Base;

use App\Models\UUserAccountd;
use App\Models\Payment;


/**
 *  foke / 主办方表
 * Class DanmuList
 */
class SUser extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName     = 's_user';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';


   /**
     * 修改奖励金额
     * 
     * @param int    $orderId 订单号
     * @param int    $payId   支付id
     * @param int    $uid     user's id
     * @param number $account 金额
     *
     * @return false | array.
     */
    public function amountUpdate($orderId, $payId, $uid, $account)
    {
        $userData =  $this->get($uid);

        if(!$userData) {
            return false;
        }

        $userAccount  = $userData['amount'] + $account;

        $updateStatus = $userData->update(
            //update
            [
            'amount' => $userAccount,
            ], 
            //where
            [
                'id' => $uid
            ]
        );

        if(!$updateStatus){
            return false;
        }
        
        if(!UUserAccountd::create()->giftSendAccountLog($orderId, $uid, $account)) {
            return false;
        };


        if(!Payment::create()->payOrderIdConnect($payId, $orderId)) {
            return false;
        };
        
        return true;
    }

}