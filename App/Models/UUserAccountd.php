<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

use App\Models\Base;
/**
 *  foke / 主办方余额明细表
 * Class Player
 */
class UUserAccountd extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName     = 't_uuser_account';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';

    /**
     * 直播打赏礼物明细记录
     * 
     * @param int    $orderId 订单号
     * @param int    $uid     user's id
     * @param number $account 金额
     * 
     * @return false | array.
     */
    public function giftSendAccountLog($orderId, $uid, $account)
    {

       $this->retail_amount = $account;
       $this->type          = 1;
       $this->order_id      = $orderId;
       $this->uuser_id      = $uid;
       $this->add_time      = date('Y-m-d H:i:s', time());
       $this->account_type  = 5;
       $this->other_type    = 5;
       $this->status        = 2;
       $this->desc          = '用户打赏礼物';

       
       return $this->save();
    }
}